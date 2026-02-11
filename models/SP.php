<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

/**
 * SP (Stored Procedure Wrapper)
 * 
 * Central utility class for executing SQL Server stored procedures with intelligent
 * error recovery and multi-result set handling. Provides a unified interface for all
 * database operations by encapsulating the complexity of parameterized EXEC statements,
 * fallback logic for procedure name variations, and standardized result formatting.
 * 
 * This class implements a resilient invocation pattern that supports:
 * - Dynamic parameter binding and SQL injection prevention (uses PDO prepared statements)
 * - Automatic fallback to alternate procedure names when primary name isn't found
 * - Schema-aware naming (dbo. prefix handling for linked servers)
 * - Universal result set handling compatible with sp_UI_* dual-result-set pattern
 * - Graceful error return structure for UI error handling
 */
class SP
{
    /**
     * call()
     * 
     * Executes a stored procedure with parameterized arguments and returns a standardized
     * result array with status information and data payload. 
     * 
     * EXECUTION FLOW:
     * 1. Gets database connection from DB singleton
     * 2. Builds parameterized EXEC statement with @parameter notation
     * 3. Attempts to execute against primary procedure name
     * 4. On "procedure not found" error, applies intelligent fallback strategy:
     *    - Strips/adds dbo. schema prefix (handles linked server naming)
     *    - Converts sp_UI_* names to sp_* equivalents (old/new naming schemes)
     *    - Substitutes common legacy names (e.g., catalog procedure alternatives)
     * 5. Parses all result sets from MULTI_RESULTSET-capable procedures
     * 6. Distinguishes between Status/Message rows (control information) and Data rows
     * 7. Returns unified array structure for consistent UI integration
     * 
     * PARAMETER BINDING:
     * - All @parameters use named placeholders with PDO binding to prevent SQL injection
     * - Parameters passed as associative array keys matching SQL Server variable names
     * - Example: call('sp_Usuarios_Obtener', ['UsuarioID' => 5]) 
     *   becomes: EXEC sp_Usuarios_Obtener @UsuarioID = :UsuarioID
     * 
     * RESULT SET HANDLING:
     * - Many sp_UI_* procedures return dual result sets:
     *   First set: Single row with [Status, Message] + optional metadata fields
     *   Remaining sets: Actual data rows (variable count)
     * - Legacy procedures may return data directly without Status/Message wrapper
     * - Method processes all available result sets via nextRowset() iteration
     * - Automatically detects and extracts Status/Message from first or second result set
     * - Merges additional result sets into Data array maintaining row order
     * - Preserves extra fields from Status row (e.g., Action codes, entity IDs)
     * 
     * FALLBACK STRATEGY:
     * When PDOException indicates procedure not found, attempts these candidates:
     * 1. Strips dbo. prefix if present (tests both with/without schema)
     * 2. Replaces sp_UI_ prefix with sp_ (handles naming convention transitions)
     * 3. Tests known legacy aliases (e.g., catalog procedure names)
     * Each candidate is tested until one succeeds; only then are results processed.
     * If all candidates fail, returns ERROR status with original error message.
     * 
     * ERROR HANDLING STRATEGY:
     * - PDOException during execution → Returns structured error array (non-throwing)
     * - Throwable during connection → Returns structured error array (non-throwing)
     * - No data returned → Returns empty Data array with appropriate Status
     * - This design allows UI layer to handle errors uniformly without try-catch blocks
     * 
     * RETURN STRUCTURE:
     * Always returns associative array with guaranteed structure:
     * [
     *     'Status' => string|null  // 'SUCCESS', 'ERROR', or null if no status row in result
     *     'Message' => string|null // Error/warning message from database or PHP exception
     *     'Data' => array,         // Rows returned by procedure (empty if none)
     *     ... additional fields from Status row preserved as-is (e.g., 'Action', 'ID')
     * ]
     * 
     * @param string $spName Complete stored procedure name, optionally with schema (e.g. 'sp_Usuarios_Listar' or 'dbo.sp_Usuarios_Listar')
     * @param array $params Associative array of parameters [paramName => value], keys become @paramName in EXEC statement
     * @return array Standardized result array with Status, Message, Data keys + any extra fields from Status row
     */
    public static function call(string $spName, array $params = []): array
    {
        try {
            $cn = DB::conn();

            // Build parameterized EXEC statement: @ParamName = :ParamName notation
            // The :ParamName will be bound via PDO prepared statement placeholders
            $parts = [];
            foreach ($params as $k => $v) {
                $parts[] = "@$k = :$k";
            }

            $sql = "EXEC $spName " . implode(', ', $parts);

            try {
                // Prepare and execute the parameterized statement
                $stmt = $cn->prepare($sql);
                foreach ($params as $k => $v) {
                    $stmt->bindValue(":$k", $v);
                }
                $stmt->execute();
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                // Detect "procedure not found" errors to trigger fallback strategy
                if (stripos($msg, 'could not find stored procedure') !== false || stripos($msg, 'could not find') !== false) {
                    $tried = [];
                    // Schema prefix variations: with/without dbo. prefix
                    if (stripos($spName, 'dbo.') === 0) {
                        $tried[] = substr($spName, 4);
                    } else {
                        $tried[] = 'dbo.' . $spName;
                    }

                    // Naming convention fallback: sp_UI_* → sp_* (handles legacy procedure renames)
                    if (stripos($spName, 'sp_UI_') !== false) {
                        $tried[] = str_ireplace('sp_UI_', 'sp_', $spName);
                    }

                    // Known legacy procedure aliases for backward compatibility
                    if (stripos($spName, 'catalog') !== false) {
                        $tried[] = 'sp_Catalogo_ListarJuegos';
                    }

                    // Attempt each fallback candidate until one executes successfully
                    $executed = false;
                    foreach ($tried as $candidate) {
                        // Skip if candidate is identical to already-failed spName
                        if (in_array($candidate, [$spName], true)) continue;
                        try {
                            $sql2 = "EXEC $candidate " . implode(', ', $parts);
                            $stmt = $cn->prepare($sql2);
                            foreach ($params as $k => $v) {
                                $stmt->bindValue(":$k", $v);
                            }
                            $stmt->execute();
                            $executed = true;
                            break;
                        } catch (PDOException $e2) {
                            // This candidate failed, continue to next
                            continue;
                        }
                    }

                    // If no candidate succeeded, return error structure with original exception message
                    if (! $executed) {
                        return [
                            'Status' => 'ERROR',
                            'Message' => $msg,
                            'Data' => [],
                        ];
                    }
                } else {
                    // Non-recovery-eligible error (not "procedure not found"), return immediately
                    return [
                        'Status' => 'ERROR',
                        'Message' => $e->getMessage(),
                        'Data' => [],
                    ];
                }
            }
        } catch (Throwable $ex) {
            // Connection failure or other fatal exception during setup
            // Return structured error to prevent unhandled exceptions in UI layer
            return [
                'Status' => 'ERROR',
                'Message' => $ex->getMessage(),
                'Data' => [],
            ];
        }

        // Initialize standardized result structure
        // Status/Message may be updated as result sets are processed
        $result = [
            'Status' => null,
            'Message' => null,
            'Data' => [],
        ];

        // Process first result set
        // Many sp_UI_* procedures return Status/Message as first row of first result set
        $first = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($first) && array_key_exists('Status', $first[0])) {
            // First row contains Status column → treat entire first row as control information
            $result['Status'] = $first[0]['Status'];
            $result['Message'] = $first[0]['Message'] ?? null;
            // Preserve additional fields from Status row (e.g., ActionID, EntityID, etc.)
            foreach ($first[0] as $k => $v) {
                if ($k === 'Status' || $k === 'Message') {
                    continue;
                }
                $result[$k] = $v;
            }
        } else {
            // First result set has no Status column → treat all rows as data
            if (!empty($first)) {
                $result['Data'] = $first;
            }
        }

        // Process any additional result sets (common when sp returns multiple result sets)
        // These are merged into the Data array maintaining insertion order
        while ($stmt->nextRowset()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) continue;
            // Check if this result set's first row contains Status (in case status is in 2nd set)
            if (empty($result['Data']) && array_key_exists('Status', $rows[0])) {
                $result['Status'] = $rows[0]['Status'];
                $result['Message'] = $rows[0]['Message'] ?? $result['Message'];
                foreach ($rows[0] as $k => $v) {
                    if ($k === 'Status' || $k === 'Message') {
                        continue;
                    }
                    $result[$k] = $v;
                }
                continue;
            }
            // Append all rows from this result set to Data array
            foreach ($rows as $r) {
                $result['Data'][] = $r;
            }
        }

        return $result;
    }
}

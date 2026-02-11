<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

class SP
{
    public static function call(string $spName, array $params = []): array
    {
        try {
            $cn = DB::conn();

            $parts = [];
            foreach ($params as $k => $v) {
                $parts[] = "@$k = :$k";
            }

            $sql = "EXEC $spName " . implode(', ', $parts);

            try {
                $stmt = $cn->prepare($sql);
                foreach ($params as $k => $v) {
                    $stmt->bindValue(":$k", $v);
                }
                $stmt->execute();
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                // If stored procedure not found, try sensible fallbacks
                if (stripos($msg, 'could not find stored procedure') !== false || stripos($msg, 'could not find') !== false) {
                    $tried = [];
                    // candidate patterns
                    if (stripos($spName, 'dbo.') === 0) {
                        $tried[] = substr($spName, 4);
                    } else {
                        $tried[] = 'dbo.' . $spName;
                    }

                    if (stripos($spName, 'sp_UI_') !== false) {
                        $tried[] = str_ireplace('sp_UI_', 'sp_', $spName);
                    }

                    // common legacy name for catalog
                    if (stripos($spName, 'catalog') !== false) {
                        $tried[] = 'sp_Catalogo_ListarJuegos';
                    }

                    // attempt fallbacks
                    $executed = false;
                    foreach ($tried as $candidate) {
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
                            // continue to next candidate
                            continue;
                        }
                    }

                    if (! $executed) {
                        return [
                            'Status' => 'ERROR',
                            'Message' => $msg,
                            'Data' => [],
                        ];
                    }
                } else {
                    return [
                        'Status' => 'ERROR',
                        'Message' => $e->getMessage(),
                        'Data' => [],
                    ];
                }
            }
        } catch (Throwable $ex) {
            // DB connection error or other fatal; return structured error for graceful UI
            return [
                'Status' => 'ERROR',
                'Message' => $ex->getMessage(),
                'Data' => [],
            ];
        }

        // Many sp_UI_* return two resultsets: first a Status/Message row, then the dataset.
        $result = [
            'Status' => null,
            'Message' => null,
            'Data' => [],
        ];

        // First result set
        $first = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($first) && array_key_exists('Status', $first[0])) {
            $result['Status'] = $first[0]['Status'];
            $result['Message'] = $first[0]['Message'] ?? null;
            // Preserve any extra fields returned in the status row (e.g. Action)
            foreach ($first[0] as $k => $v) {
                if ($k === 'Status' || $k === 'Message') {
                    continue;
                }
                $result[$k] = $v;
            }
        } else {
            // If first set isn't the status, treat it as data
            if (!empty($first)) {
                $result['Data'] = $first;
            }
        }

        // If there are additional result sets, merge them into Data
        while ($stmt->nextRowset()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) continue;
            // If Data is empty and this looks like the Status row, skip (already handled)
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
            // append rows
            foreach ($rows as $r) {
                $result['Data'][] = $r;
            }
        }

        return $result;
    }
}

<?php
// Long-running daemon to process pending PDF report jobs in DB.
// Usage: php pdf_worker_daemon.php
set_time_limit(0);
$root = dirname(__DIR__);
// load DB config
$dbconf = include $root . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
$group = isset($dbconf['default']) ? $dbconf['default'] : $dbconf;
$host = $group['hostname'];
$user = $group['username'];
$pass = $group['password'];
$db = $group['database'];
$interval = 5; // seconds between polls
$php = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
$workerScript = $root . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'pdf_worker.php';
while (true) {
    try {
        $mysqli = new mysqli($host, $user, $pass, $db);
        if ($mysqli->connect_errno) {
            echo "DB connect error: " . $mysqli->connect_error . "\n";
            sleep($interval);
            continue;
        }
        // fetch one pending job
        $res = $mysqli->query("SELECT job_id, print_url, file_path FROM tb_reports WHERE status='pending' ORDER BY created_at ASC LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $job = $row['job_id'];
            $printUrl = $row['print_url'];
            $outRel = $row['file_path'];
            $outFull = $root . DIRECTORY_SEPARATOR . $outRel;
            // mark as processing
            $now = date('Y-m-d H:i:s');
            $stmt = $mysqli->prepare("UPDATE tb_reports SET status='processing', started_at=? WHERE job_id=?");
            $stmt->bind_param('ss', $now, $job);
            $stmt->execute();
            $stmt->close();
            // spawn worker for this job
            $cmd = '"' . $php . '" "' . $workerScript . '" ' . escapeshellarg($job) . ' ' . escapeshellarg($printUrl) . ' ' . escapeshellarg($outFull);
            // run synchronously to keep controlled worker
            exec($cmd . ' 2>&1', $output, $rc);
            // if worker returns non-zero, mark error
            if ($rc !== 0) {
                $err = implode("\n", $output);
                $stmt = $mysqli->prepare("UPDATE tb_reports SET status='error', finished_at=?, error_text=? WHERE job_id=?");
                $now2 = date('Y-m-d H:i:s');
                $stmt->bind_param('sss', $now2, $err, $job);
                $stmt->execute();
                $stmt->close();
                echo "Job $job failed: $err\n";
            } else {
                // worker will have reported done via callback; ensure it's done
                echo "Job $job processed.\n";
            }
        }
        $mysqli->close();
    } catch (Exception $e) {
        echo "Daemon exception: " . $e->getMessage() . "\n";
    }
    sleep($interval);
}

*** End Patch
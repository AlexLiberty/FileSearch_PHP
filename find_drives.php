<?php
function getAvailableDrives()
{
    $drives = [];
    $os = strtoupper(substr(PHP_OS, 0, 3));

    if ($os === 'WIN') {
        $output = shell_exec('wmic logicaldisk get caption');
        $lines = explode("\n", trim($output));
        array_shift($lines);

        foreach ($lines as $line)
        {
            $drive = trim($line);
            if (!empty($drive))
            {
                $drives[] = $drive;
            }
        }
    } elseif ($os === 'DAR') {
        $output = shell_exec('df -H | grep "^/dev/"');
        $lines = explode("\n", trim($output));

        foreach ($lines as $line)
        {
            $parts = preg_split('/\s+/', $line);
            if (!empty($parts[0]))
            {
                $drives[] = $parts[0];
            }
        }
    } elseif ($os === 'LIN') {
        $output = shell_exec('df -H --output=source | grep "^/dev/"');
        $lines = explode("\n", trim($output));

        foreach ($lines as $line)
        {
            $drive = trim($line);
            if (!empty($drive))
            {
                $drives[] = $drive;
            }
        }
    }

    return $drives;
}

$availableDrives = getAvailableDrives();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вибір диска та пошук файлів</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Виберіть диск і параметри пошуку</h2>
    <form action="search_files.php" method="post">
        <label>Диск:</label>
        <select name="drive" required>
            <?php foreach ($availableDrives as $drive): ?>
                <option value="<?= htmlspecialchars($drive) ?>"><?= htmlspecialchars($drive) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label>Маска файлів (необов'язково):</label>
        <input type="text" name="file_mask" placeholder="Наприклад, *.txt"><br>

        <label>Текст для пошуку (необов'язково):</label>
        <input type="text" name="search_text" placeholder="Введіть текст для пошуку"><br>

        <input type="submit" value="Знайти файли">
    </form>
</div>
</body>
</html>

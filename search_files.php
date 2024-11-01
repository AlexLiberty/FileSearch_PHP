<?php

$drive = $_POST['drive'] ?? '';
$fileMask = $_POST['file_mask'] ?? '*';
$searchText = $_POST['search_text'] ?? '';

if (empty($drive))
{
    die("Помилка: Оберіть диск.");
}

$maxDepth = 3;

function findFiles($directory, $mask, $currentDepth = 0, $maxDepth = 3)
{
    $files = glob($directory . '/' . $mask);

    if ($currentDepth >= $maxDepth)
    {
        return $files;
    }

    foreach (glob($directory . '/*', GLOB_ONLYDIR) as $dir)
    {
        if (is_dir($dir) && !in_array(basename($dir), ['System Volume Information', '$RECYCLE.BIN']))
        {
            $files = array_merge($files, findFiles($dir, $mask, $currentDepth + 1, $maxDepth));
        }
    }
    return $files;
}

function searchInFile($file, $searchText)
{
    $positions = [];
    $handle = fopen($file, "r");
    if ($handle)
    {
        $lineNumber = 0;
        while (($line = fgets($handle)) !== false)
        {
            $lineNumber++;
            $offset = 0;
            while (($pos = strpos($line, $searchText, $offset)) !== false)
            {
                $positions[] = "Рядок {$lineNumber}, позиція {$pos}";
                $offset = $pos + strlen($searchText);
            }
        }
        fclose($handle);
    }
    return $positions;
}

$directory = rtrim($drive, '/');
$files = findFiles($directory, $fileMask, 0, $maxDepth);

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Результати пошуку файлів</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Результати пошуку файлів</h2>
    <?php
    if (empty($files))
    {
        echo "<p>Файли не знайдено за маскою $fileMask на диску $drive</p>";
    }
    else
    {
        echo "<p>Знайдено файлів: " . count($files) . "</p>";
        echo "<ul>";
        foreach ($files as $file)
        {
            echo "<li><strong>Файл:</strong> " . htmlspecialchars($file);

            if (!empty($searchText))
            {
                $positions = searchInFile($file, $searchText);
                if (!empty($positions))
                {
                    echo "<ul><li><strong>Знайдено:</strong><ul>";
                    foreach ($positions as $pos)
                    {
                        echo "<li>" . htmlspecialchars($pos) . "</li>";
                    }
                    echo "</ul></li></ul>";
                }
                else
                {
                    echo " - текст не знайдено.";
                }
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    ?>
    <button onclick="window.location.href='find_drives.php'">Повернутися до вибору диска</button>
</div>
</body>
</html>

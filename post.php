<?php
require_once 'config.php';

// --- Template Engine Function (Same as in index.php) ---
function renderTemplate($templateFile, $data) {
    $template = file_get_contents($templateFile);

    // Handle loops (very basic)
    preg_match_all('/{{(.*?)}}(.*?){{\/(.*?)}}/s', $template, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        list(, $loopVar, $loopContent, $closeLoopVar) = $match;

        if ($loopVar == $closeLoopVar && isset($data[$loopVar])) {
            $loopOutput = '';
            foreach ($data[$loopVar] as $item) {
                $itemOutput = $loopContent;
                foreach ($item as $key => $value) {
                    $itemOutput = str_replace('{{' . $key . '}}', $value, $itemOutput);
                }
                $loopOutput .= $itemOutput;
            }
            $template = str_replace($match[0], $loopOutput, $template);
        }
    }


    // Handle variables
    foreach ($data as $key => $value) {
        if(!is_array($value)) { //avoid replace loop variables.
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
    }

    return $template;
}
// --- Fetch Post ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT title, content, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        // Handle post not found (e.g., redirect to 404 page)
        header("HTTP/1.0 404 Not Found");
        echo "Post not found.";
        exit;
    }
} else {
    // Handle missing ID (e.g., redirect to homepage)
    header("Location: index.php");
    exit;
}

// --- Render the Page ---
$singleContent = renderTemplate('templates/single.html', $post);
$layoutData = [
    'title' => $post['title'],
    'content' => $singleContent
];
echo renderTemplate('templates/layout.html', $layoutData);
?>
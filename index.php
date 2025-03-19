<?php
require_once 'config.php';

// --- Template Engine Function ---
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


// --- Fetch Posts ---
$stmt = $pdo->query("SELECT id, title, content, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create excerpts
foreach ($posts as &$post) {  // Use & to modify the original array
    $post['excerpt'] = substr(strip_tags($post['content']), 0, 150) . '...'; // Limit to 150 chars
}
unset($post); // Unset the reference to avoid unexpected behavior

// --- Render the Page ---
$homeContent = renderTemplate('templates/home.html', ['posts' => $posts]);
$layoutData = [
    'title' => 'My Blog',
    'content' => $homeContent
];
echo renderTemplate('templates/layout.html', $layoutData);

?>
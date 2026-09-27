<?php
require_once 'config.php';
requireLogin();

// Get user's tasks
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY status, due_date");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

// Generate text-based report
$content = "TO-DO LIST REPORT\n";
$content .= "Generated for: " . $_SESSION['user_name'] . "\n";
$content .= "Date: " . date('F d, Y') . "\n";
$content .= str_repeat("=", 50) . "\n\n";

if (empty($tasks)) {
    $content .= "No tasks found.\n";
} else {
    $totalTasks = count($tasks);
    $completedTasks = count(array_filter($tasks, function($task) { return $task['status'] === 'Completed'; }));
    $pendingTasks = $totalTasks - $completedTasks;
    
    $content .= "SUMMARY:\n";
    $content .= "Total Tasks: $totalTasks\n";
    $content .= "Completed: $completedTasks\n";
    $content .= "Pending: $pendingTasks\n\n";
    
    $content .= "TASK LIST:\n";
    $content .= str_repeat("-", 50) . "\n";
    
    foreach ($tasks as $task) {
        $content .= "Title: " . $task['title'] . "\n";
        $content .= "Description: " . ($task['description'] ?: 'No description') . "\n";
        $content .= "Due Date: " . ($task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : 'No due date') . "\n";
        $content .= "Status: " . $task['status'] . "\n";
        $content .= "Created: " . date('M d, Y', strtotime($task['created_at'])) . "\n";
        $content .= str_repeat("-", 30) . "\n";
    }
}

// Output as downloadable text file
$filename = 'TodoList_' . $_SESSION['user_name'] . '_' . date('Y-m-d') . '.txt';
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));
echo $content;
exit();

?>
<?php
require_once 'config.php';
requireLogin();

// Handle task operations
if ($_POST) {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $due_date = $_POST['due_date'];
        
        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $due_date]);
        }
    } elseif ($action === 'toggle') {
        $task_id = $_POST['task_id'];
        $new_status = $_POST['status'] === 'Pending' ? 'Completed' : 'Pending';
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$new_status, $task_id, $_SESSION['user_id']]);
    } elseif ($action === 'delete') {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$task_id, $_SESSION['user_id']]);
    }
    
    header('Location: dashboard.php');
    exit();
}

// Get user's tasks
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - To-Do App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-tasks me-2"></i>TaskFlow</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3"><i class="fas fa-user me-1"></i>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a class="btn btn-outline-light btn-sm" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="container">
            <?php
            $totalTasks = count($tasks);
            $completedTasks = count(array_filter($tasks, function($task) { return $task['status'] === 'Completed'; }));
            $pendingTasks = $totalTasks - $completedTasks;
            ?>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $totalTasks; ?></div>
                        <div class="stats-label"><i class="fas fa-list me-1"></i>Total Tasks</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: var(--success-color);"><?php echo $completedTasks; ?></div>
                        <div class="stats-label"><i class="fas fa-check-circle me-1"></i>Completed</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: var(--warning-color);"><?php echo $pendingTasks; ?></div>
                        <div class="stats-label"><i class="fas fa-clock me-1"></i>Pending</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: var(--primary-color);"><?php echo $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0; ?>%</div>
                        <div class="stats-label"><i class="fas fa-chart-pie me-1"></i>Progress</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="task-card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus-circle me-2"></i>Add New Task</h5>
                        </div>
                        <div class="card-body add-task-form">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Task</button>
                        </form>
                    </div>
                </div>
            </div>
                
                <div class="col-md-8">
                    <div class="task-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-tasks me-2"></i>My Tasks</h5>
                            <a href="export_pdf.php" class="btn btn-success btn-sm"><i class="fas fa-download me-1"></i>Export Tasks</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($tasks)): ?>
                                <div class="no-tasks">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h5>No tasks yet</h5>
                                    <p>Add your first task to get started!</p>
                                </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tasks as $task): ?>
                                            <tr class="<?php echo $task['status'] === 'Completed' ? 'task-row-completed' : ''; ?>">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                                                    <?php if ($task['status'] === 'Completed'): ?>
                                                        <i class="fas fa-check-circle text-success ms-1"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($task['description'] ?: 'No description'); ?></td>
                                                <td>
                                                    <?php if ($task['due_date']): ?>
                                                        <i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No due date</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $task['status'] === 'Completed' ? 'bg-success' : 'bg-warning'; ?>">
                                                        <i class="fas <?php echo $task['status'] === 'Completed' ? 'fa-check' : 'fa-clock'; ?> me-1"></i>
                                                        <?php echo $task['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle">
                                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                        <input type="hidden" name="status" value="<?php echo $task['status']; ?>">
                                                        <button type="submit" class="btn btn-sm <?php echo $task['status'] === 'Completed' ? 'btn-warning' : 'btn-success'; ?>" title="<?php echo $task['status'] === 'Completed' ? 'Mark as Pending' : 'Mark as Complete'; ?>">
                                                            <i class="fas <?php echo $task['status'] === 'Completed' ? 'fa-undo' : 'fa-check'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this task?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Task">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
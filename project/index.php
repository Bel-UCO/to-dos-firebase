<?php
require 'firebase_config.php';

$isLoggedIn = false;
$authError = '';
$displayName = '';

if (!empty($_COOKIE['firebase_token'])) {
    try {
        $verifiedIdToken = $auth->verifyIdToken($_COOKIE['firebase_token']);
        $isLoggedIn = true;

        $uid = $verifiedIdToken->claims()->get('sub');
        $user = $auth->getUser($uid);
        $displayName = $user->displayName ?? $user->email ?? 'User';

    } catch (Exception $e) {
        $isLoggedIn = false;
        $authError = 'Your login has expired. Please login again.';
        setcookie("firebase_token", "", time() - 3600, "/");
    }
}

$tasks = $database->getReference('tasks')->getValue();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TO DO LIST</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php if ($authError != ''): ?>
        <div class="alert error">
            <?= htmlspecialchars($authError) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert error">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <div class="header">
        <h1>TO-DO LIST</h1>

        <div style="display:flex; gap:10px; align-items:center;">
            <?php if ($isLoggedIn): ?>
                <span>Hello, <?= htmlspecialchars($displayName) ?></span>
                <button class="add-btn" onclick="openPopup()">+</button>
                <a href="logout.php">
                    <button type="button">Logout</button>
                </a>
            <?php else: ?>
                <button type="button" onclick="openAuthPopup('login')">Login</button>
                <button type="button" onclick="openAuthPopup('register')">Register</button>
                <button class="add-btn" type="button" onclick="openAuthPopup('login')">+</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="line"></div>

    <div>
        <div class="header">
            <h5 class="title"><b>Title</b></h5>
            <h5 class="desc"><b>Description</b></h5>
            <h5 class="deadline"><b>Deadline</b></h5>
            <h5 class="priority"><b>Priority</b></h5>
            <h5 class="status"><b>Status</b></h5>
            <h5 class="actions"><b>Actions</b></h5>
        </div>

        <div class="line"></div>

        <?php if ($tasks == null): ?>
            <p style="text-align: center;">No tasks found.</p>
        <?php else: ?>
            <?php foreach ($tasks as $id => $task): ?>
                <div class="task header">
                    <p class="title"><b><?= htmlspecialchars($task['title']) ?></b></p>
                    <p class="desc"><?= htmlspecialchars($task['description']) ?></p>
                    <p class="deadline"><?= htmlspecialchars($task['deadline']) ?></p>
                    <p class="priority">
                        <?php if ($task['priority'] === 'high'): ?>
                            <span style="color: red;"><?= htmlspecialchars($task['priority']) ?></span>
                        <?php elseif ($task['priority'] === 'medium'): ?>
                            <span style="color: yellow;"><?= htmlspecialchars($task['priority']) ?></span>
                        <?php else: ?>
                            <span style="color: green;"><?= htmlspecialchars($task['priority']) ?></span>
                        <?php endif; ?>
                    </p>
                    <p class="status">
                        <?php if ($task['status'] === 'completed'): ?>
                            <span style="color: green;"><?= htmlspecialchars($task['status']) ?></span>
                        <?php else: ?>
                            <span style="color: red;"><?= htmlspecialchars($task['status']) ?></span>
                        <?php endif; ?>
                    </p>

                    <div class="actions">
                        <?php if ($isLoggedIn): ?>
                            <button onclick="openEditPopup(
                                '<?= htmlspecialchars($id, ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($task['title'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($task['description'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($task['deadline'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($task['priority'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($task['status'], ENT_QUOTES) ?>'
                            )">Edit</button>

                            <form action="delete_task.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                                <button type="submit" class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this task?');">
                                    Delete
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" onclick="openAuthPopup('login')">Edit</button>
                            <button type="button" class="delete-btn" onclick="openAuthPopup('login')">Delete</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="line" style="height: 1px;"></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="popup" id="taskPopup">
        <div class="popup-content">

            <div class="popup-header">
                <h3 id="popupTitle">Add Task</h3>
                <button type="button" class="close-btn" onclick="closePopup()">×</button>
            </div>

            <form id="taskForm" method="POST" action="add_task.php">
                <input type="hidden" name="task_id" id="task_id">

                <label>Title</label>
                <input type="text" name="title" id="title" required>

                <label>Description</label>
                <textarea name="description" id="description" required></textarea>

                <label>Deadline</label>
                <input type="date" name="deadline" id="deadline" required>

                <label>Priority</label>
                <select name="priority" id="priority">
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>

                <label>Status</label>
                <select name="status" id="status">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>

                <div class="popup-actions">
                    <button type="button" onclick="closePopup()">Cancel</button>
                    <button type="submit" id="submitBtn">Add Task</button>
                </div>
            </form>
        </div>
    </div>

    <div class="popup" id="authPopup">
        <div class="popup-content">
            <div class="popup-header">
                <h3 id="authPopupTitle">Login</h3>
                <button type="button" class="close-btn" onclick="closeAuthPopup()">×</button>
            </div>

            <div class="auth-tabs">
                <button type="button" onclick="switchAuthTab('login')">Login</button>
                <button type="button" onclick="switchAuthTab('register')">Register</button>
            </div>

            <form id="loginForm" method="POST" action="login.php">
                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <div class="popup-actions">
                    <button type="button" onclick="closeAuthPopup()">Cancel</button>
                    <button type="submit">Login</button>
                </div>
            </form>

            <form id="registerForm" method="POST" action="register.php" style="display:none;">
                <label>Display Name</label>
                <input type="text" name="displayName" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <div class="popup-actions">
                    <button type="button" onclick="closeAuthPopup()">Cancel</button>
                    <button type="submit">Register</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

        function openPopup() {
            if (!isLoggedIn) {
                openAuthPopup('login');
                return;
            }

            document.getElementById("popupTitle").innerText = "Add Task";
            document.getElementById("taskForm").action = "add_task.php";
            document.getElementById("submitBtn").innerText = "Add Task";

            document.getElementById("task_id").value = "";
            document.getElementById("title").value = "";
            document.getElementById("description").value = "";
            document.getElementById("deadline").value = "";
            document.getElementById("priority").value = "low";
            document.getElementById("status").value = "pending";

            document.getElementById("taskPopup").style.display = "flex";
        }

        function openEditPopup(id, title, description, deadline, priority, status) {
            if (!isLoggedIn) {
                openAuthPopup('login');
                return;
            }

            document.getElementById("popupTitle").innerText = "Edit Task";
            document.getElementById("taskForm").action = "update_task.php";
            document.getElementById("submitBtn").innerText = "Save";

            document.getElementById("task_id").value = id;
            document.getElementById("title").value = title;
            document.getElementById("description").value = description;
            document.getElementById("deadline").value = deadline;
            document.getElementById("priority").value = priority;
            document.getElementById("status").value = status;

            document.getElementById("taskPopup").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("taskPopup").style.display = "none";
        }

        function openAuthPopup(tab = 'login') {
            if (isLoggedIn) return;
            document.getElementById("authPopup").style.display = "flex";
            switchAuthTab(tab);
        }

        function closeAuthPopup() {
            document.getElementById("authPopup").style.display = "none";
        }

        function switchAuthTab(tab) {
            const loginForm = document.getElementById("loginForm");
            const registerForm = document.getElementById("registerForm");
            const title = document.getElementById("authPopupTitle");

            if (tab === 'register') {
                loginForm.style.display = "none";
                registerForm.style.display = "flex";
                registerForm.style.flexDirection = "column";
                title.innerText = "Register";
            } else {
                loginForm.style.display = "flex";
                loginForm.style.flexDirection = "column";
                registerForm.style.display = "none";
                title.innerText = "Login";
            }
        }

        window.onclick = function(event) {
            const taskPopup = document.getElementById("taskPopup");
            const authPopup = document.getElementById("authPopup");

            if (event.target === taskPopup) {
                taskPopup.style.display = "none";
            }

            if (event.target === authPopup) {
                authPopup.style.display = "none";
            }
        }
    </script>

</body>
</html>
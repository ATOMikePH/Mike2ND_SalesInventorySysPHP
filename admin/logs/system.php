<style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        color: #495057;
    }

    .whiteboard {
        width: 80%;
        margin: 20px auto;
        background-color: #ffffff;
        border: 2px solid #607d8b;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto; /* Add vertical scroll */
        max-height: 650px; /* Limit height and add scroll */
        position: relative; /* Add relative positioning */
    }

    h2 {
        color: #3f51b5;
        text-align: center;
        top: 0; /* At the top of the container */
        background-color: #ffffff; /* Match the whiteboard background */
        padding: 10px 0; /* Add some padding for aesthetics */
    }

    #filterButtons {
        text-align: center;
        margin-bottom: 10px;
    }

    #filterButtons button {
        margin: 5px;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
    }

    #filterButtons button.active {
        background-color: #343a40;
        color: #ffffff;
    }

    #filterButtons button:hover {
        background-color: #343a40;
        color: #ffffff;
    }

    .log-entry {
        margin-bottom: 15px;
        padding: 15px;
        border: 1px solid #20c997;
        border-radius: 8px;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease-in-out, opacity 0.3s ease-in-out; /* Add opacity transition */
    }

    .log-entry.hidden {
        opacity: 0; /* Make hidden logs transparent */
        height: 0;
        margin: 0;
        padding: 0;
    }

    .log-entry:hover {
        background-color: #FCD299;
        opacity: 1; /* Make the hovered log fully opaque */
    }

    .datetime {
        color: #6a72eb;
        font-weight: bold;
    }

    .user {
        color: #343a40;
        font-weight: bold;
    }

    .action-type {
        font-weight: normal;
        color: #007bff;
    }

    .data-name {
        color: #343a40;
        font-weight: bold;
    }

    .section-name {
        font-weight: normal;
        color: #343a40;
    }

    #noLogsMessage {
        font-size: 1.5em;
        color: #868e96;
        text-align: center;
        margin-top: 20px;
        font-style: italic;
    }

    #noLogsImage {
        display: none;
        margin: 20px auto;
        max-width: 100%;
        height: 300px;
    }

</style>
<body>
    <div class="whiteboard">
        <h2>System Logs</h2>

        <!-- Add Filter Buttons -->
    <div id="filterButtons">
    <button class="active" onclick="filterLogs('All')">All Time</button>
    <button onclick="filterLogs('Today')">Today</button>
    <button onclick="filterLogs('Yesterday')">Yesterday</button>
    <button onclick="filterLogs('LastWeek')">Last Week</button>
    <button onclick="filterLogs('LastMonth')">Last Month</button>
    <button onclick="filterLogs('LastYear')">Last Year</button>
</div>

        <div id="logsContainer"></div>
        <div id="noLogsMessage" style="display: none;">No activity logs found.</div>
        <img id="noLogsImage" src="logs/no_found.png" alt="">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let allLogs = []; // Variable to store all logs

            const fetchUserLogs = async () => {
                try {
                    const response = await fetch('fetch_system_logs.php'); // Adjust the path based on your file structure
                    const logs = await response.json();
                    allLogs = logs; // Store logs in the variable
                    displayLogs(logs);
                } catch (error) {
                    console.error('Error fetching system logs:', error);
                }
            };

            const generateFormattedUserName = (user) => {
                const firstNameInitial = user.firstname.charAt(0).toUpperCase();
                const lastName = user.lastname;

                return `${firstNameInitial}. ${lastName}`;
            };

            const displayLogs = (logs) => {
                const logsContainer = document.getElementById('logsContainer');
                const noLogsMessage = document.getElementById('noLogsMessage');
                const noLogsImage = document.getElementById('noLogsImage');
                logsContainer.innerHTML = ''; // Clear previous logs

                if (logs.length === 0) {
                    noLogsMessage.style.display = 'block';
                    noLogsImage.style.display = 'block';
                } else {
                    noLogsMessage.style.display = 'none';
                    noLogsImage.style.display = 'none';
                    logs.forEach(log => {
                        const logEntry = document.createElement('div');
                        logEntry.classList.add('log-entry');

                        const datetime = `<span class="datetime">${log.datetime}</span>`;
                        const user = `<span class="user">User ${generateFormattedUserName(log)}</span>`;
                        const actionType = `<span class="action-type">${log.action_description}</span>`;

                        logEntry.innerHTML = `${datetime} : ${user} ${actionType}`;
                        logsContainer.appendChild(logEntry);
                    });
                }
            };

            const filterLogs = (filter) => {
                let filteredLogs;

                switch (filter) {
                    case 'Today':
                        filteredLogs = allLogs.filter(log => isToday(new Date(log.datetime)));
                        break;
                    case 'Yesterday':
                        filteredLogs = allLogs.filter(log => isYesterday(new Date(log.datetime)));
                        break;
                    case 'LastWeek':
                        filteredLogs = allLogs.filter(log => isLastWeek(new Date(log.datetime)));
                        break;
                    case 'LastMonth':
                        filteredLogs = allLogs.filter(log => isLastMonth(new Date(log.datetime)));
                        break;
                        case 'LastYear':
                        filteredLogs = allLogs.filter(log => isLastYear(new Date(log.datetime)));
                        break;
                    default:
                        filteredLogs = allLogs; // All Time
                        break;
                }

                // Hide all logs with the hidden class first
                document.querySelectorAll('.log-entry').forEach(entry => {
                    entry.classList.add('hidden');
                });

                // Display the filtered logs or show the noLogsMessage and noLogsImage
                setTimeout(() => {
                    displayLogs(filteredLogs);
                }, 300);
            };

            const isToday = (date) => {
                const today = new Date();
                return date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
            };

            const isYesterday = (date) => {
                const yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);
                return date.getDate() === yesterday.getDate() && date.getMonth() === yesterday.getMonth() && date.getFullYear() === yesterday.getFullYear();
            };

            const isLastWeek = (date) => {
                const today = new Date();
                const lastWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);
                return date >= lastWeek;
            };

            const isLastMonth = (date) => {
                const today = new Date();
                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
                return date >= lastMonth;
            };

            const isLastYear = (date) => {
                const today = new Date();
                const lastYear = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
                return date >= lastYear;
            };

            const filterButtons = document.getElementById('filterButtons');
        filterButtons.addEventListener('click', (event) => {
            if (event.target.tagName === 'BUTTON') {
                filterButtons.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
                filterLogs(event.target.textContent);
            }
        });
            fetchUserLogs();
        });
    </script>
</body>
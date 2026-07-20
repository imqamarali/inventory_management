<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>WebixSchool Ticket Dashboard</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .dashboard-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .dashboard-title {
            font-size: 18px;
            margin: 0;
            color: #2563eb;
            font-weight: bold;
        }

        .dashboard-subtitle {
            font-size: 12px;
            color: #6b7280;
            display: block;
            margin-top: 4px;
        }

        .my-ticket-dashboard {
            width: 280px;
            padding: 20px;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            height: 100vh;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .my-ticket-dashboard .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .my-ticket-dashboard .search-input {
            flex: 1;
            padding: 8px 10px;
            font-size: 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: border-color 0.2s;
        }

        .my-ticket-dashboard .search-input:focus {
            border-color: #2563eb;
            outline: none;
        }

        .my-ticket-dashboard .search-submit,
        .my-ticket-dashboard .search-clear {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            font-size: 16px;
            color: #6b7280;
            transition: color 0.2s;
        }

        .my-ticket-dashboard .search-submit:hover,
        .my-ticket-dashboard .search-clear:hover {
            color: #111827;
        }

        .my-ticket-dashboard .section-title {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            margin-top: 30px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .my-ticket-dashboard ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .my-ticket-dashboard li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s, padding-left 0.2s;
        }

        .my-ticket-dashboard li:hover {
            background-color: #f3f4f6;
            padding-left: 12px;
        }

        .my-ticket-dashboard li.active {
            background-color: #e0f2fe;
            padding-left: 12px;
            font-weight: 600;
            color: #1d4ed8;
        }

        .my-ticket-dashboard .badge {
            background-color: #e5e7eb;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 12px;
            color: #374151;
            font-weight: 500;
        }

        .my-ticket-dashboard .manage-link {
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
            margin-top: 5px;
            display: inline-block;
        }

        .my-ticket-dashboard .manage-link:hover {
            text-decoration: underline;
        }

        .shimmer {
            background: linear-gradient(to right, #f0f0f0 8%, #e0e0e0 18%, #f0f0f0 33%);
            background-size: 1000px 100%;
            animation: shimmer 1.5s infinite linear;
            height: 18px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .shimmer.large {
            height: 24px;
            width: 60%;
        }

        .shimmer.medium {
            width: 40%;
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        /* Mobile Responsive Styles - Horizontal Bar */
        @media (max-width: 768px) {
            .my-ticket-dashboard {
                position: fixed;
                top: 132px;
                left: 0;
                right: 0;
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                z-index: 998;
                background: white;
                padding: 12px 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .dashboard-header {
                display: none;
            }

            .search-form {
                display: none;
            }

            .section-title {
                display: none;
            }

            /* Hide section containers, show only lists */
            .ticket-statuses,
            .ticket-folders {
                display: contents;
            }

            /* Create a single scrolling container for all items */
            .my-ticket-dashboard {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                align-items: center;
                gap: 6px;
            }

            /* Make all ul elements inline-flex to flow in one continuous row */
            .my-ticket-dashboard>ul,
            .my-ticket-dashboard .ticket-statuses ul,
            .my-ticket-dashboard .ticket-folders ul {
                display: inline-flex;
                flex-direction: row;
                padding: 0;
                margin: 0;
                gap: 6px;
                list-style: none;
                flex-shrink: 0;
            }

            /* Style like Quick Actions buttons */
            .my-ticket-dashboard li {
                flex-shrink: 0;
                white-space: nowrap;
                padding: 8px 14px;
                background: white;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                color: #2563eb;
                font-size: 11px;
                font-weight: 500;
                min-width: auto;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                transition: all 0.2s;
            }

            .my-ticket-dashboard li:hover {
                background: #f3f4f6;
                border-color: #2563eb;
                padding-left: 14px;
                color: #1d4ed8;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                transform: translateY(-1px);
            }

            .my-ticket-dashboard li.active {
                background: #2563eb;
                padding-left: 14px;
                color: white;
                font-weight: 600;
                border-color: #1d4ed8;
                box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
            }

            .my-ticket-dashboard .badge {
                background: #ef4444;
                color: white;
                font-size: 9px;
                font-weight: 600;
                padding: 2px 5px;
                margin-left: 4px;
                border-radius: 10px;
                min-width: 16px;
                text-align: center;
            }

            .my-ticket-dashboard li.active .badge {
                background: #ffd700;
                color: #333;
            }

            /* Scrollbar styling for main container */
            .my-ticket-dashboard::-webkit-scrollbar {
                height: 4px;
            }

            .my-ticket-dashboard::-webkit-scrollbar-track {
                background: #f3f4f6;
                border-radius: 2px;
            }

            .my-ticket-dashboard::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 2px;
            }

            .my-ticket-dashboard::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            #ticket-content-area {
                padding: 20px 15px !important;
                width: 100% !important;
                padding-top: 110px !important;
                margin-top: 0 !important;
            }

            .manage-link {
                display: none;
            }
        }

        @media (max-width: 480px) {
            #ticket-content-area {
                padding: 15px 10px !important;
                padding-top: 100px !important;
                margin-left: 15px !important;
                margin-right: 15px !important;
            }

            .my-ticket-dashboard li {
                font-size: 10px;
                padding: 6px 10px;
            }

            .my-ticket-dashboard .badge {
                font-size: 8px;
                padding: 1px 4px;
                margin-left: 3px;
            }
        }

        #new-ticket-button {
            position: fixed;
            top: 47px;
            left: 17%;
            width: 50px;
            height: 50px;
            background-color: #28a745;
            color: white;
            font-size: 28px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            cursor: move;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            user-select: none;
        }

        /* Custom tooltip on hover */
        #new-ticket-button::after {
            content: 'New Ticket';
            position: absolute;
            top: -35px;
            right: 0;
            transform: translateX(50%);
            background-color: #333;
            color: #fff;
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 4px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease-in-out;
        }

        #new-ticket-button:hover::after {
            opacity: 1;
        }

        /* Mobile - Adjust New Ticket Button */
        @media (max-width: 768px) {
            #new-ticket-button {
                left: auto;
                right: 15px;
                top: 120px;
                width: 50px;
                height: 50px;
                font-size: 26px;
            }
        }

        @media (max-width: 480px) {
            #new-ticket-button {
                width: 45px;
                height: 45px;
                font-size: 24px;
                right: 10px;
            }
        }
    </style>
</head>

<body>
    <div style="display: flex; height: 100vh;">
        <!-- Draggable New Ticket Button -->
        <div id="new-ticket-button" title="Create New Ticket">+</div>

        <!-- Sidebar -->
        <div class="my-ticket-dashboard">
            <div class="dashboard-header">
                <div class="dashboard-title-wrapper">
                    <div>
                        <h1 class="dashboard-title">Support Desk</h1>
                        <span class="dashboard-subtitle">Ticket Management System</span>
                    </div>
                </div>
            </div>

            <ul class="menu-list">
                <li data-section="recent" class="active">All recent tickets</li>
                <li data-section="to-handle">Tickets to handle <span class="badge"></span></li>
                <li data-section="my-open">My open tickets <span class="badge"></span></li>
                <li data-section="last-7-days">My tickets in last 7 days <span class="badge"></span></li>
            </ul>

            <div class="ticket-statuses">
                <div class="section-title">Statuses</div>
                <ul class="menu-list">
                    <li data-section="status-open">Open <span class="badge"></span></li>
                    <li data-section="status-pending">Pending <span class="badge"></span></li>
                    <li data-section="status-hold">On hold <span class="badge"></span></li>
                    <li data-section="status-solved">Solved</li>
                    <li data-section="status-closed">Closed</li>
                </ul>
            </div>

            <div class="ticket-folders">
                <div class="section-title">Folders</div>
                <ul class="menu-list">
                    <li data-section="folder-archive">Archive</li>
                    <li data-section="folder-spam">Spam</li>
                    <li data-section="folder-trash">Trash</li>
                </ul>
            </div>
        </div>

        <!-- Right Content Area -->
        <div id="ticket-content-area" style="flex: 1; padding: 30px; overflow-y: auto;">
            <h2 id="ticket-title">Welcome</h2>
            <p>Select a ticket category from the sidebar to view its details here.</p>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        window.addEventListener("beforeunload", function(e) {
            const confirmationMessage = "Are you sure you want to leave? Changes you made may not be saved.";
            (e || window.event).returnValue = confirmationMessage;
            return confirmationMessage;
        });

        document.addEventListener("keydown", function(e) {
            if ((e.key === "F5") || (e.ctrlKey && e.key === "r") || (e.metaKey && e.key === "r")) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Refresh Disabled',
                    text: 'Page refresh is disabled to prevent data loss.',
                    confirmButtonText: 'Got it',
                    confirmButtonColor: '#2563eb',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // $('#sidebar').addClass('menu-min'); // Optional: check if #sidebar exists

            const contentArea = $('#ticket-content-area');
            const menuItems = document.querySelectorAll('.menu-list li');

            // Load ticket counts for badges
            loadTicketCounts();

            menuItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Remove active class from all
                    menuItems.forEach(i => i.classList.remove('active'));
                    // Add active class to clicked item
                    item.classList.add('active');

                    const section = item.getAttribute('data-section');

                    // Loading shimmer
                    contentArea.html(`
              <h2 class="shimmer large"></h2>
              <div class="shimmer"></div>
              <div class="shimmer medium"></div>
            `);

                    // Simulate delay then load content
                    setTimeout(() => {
                        $.ajax({
                            url: 'index.php?r=support/loadsection',
                            method: 'GET',
                            data: {
                                section: section
                            },
                            success: function(response) {
                                contentArea.html(response);
                            },
                            error: function(xhr, status, error) {
                                contentArea.html(
                                    '<h2>Error</h2><p>Unable to load section.</p>'
                                );
                                console.error(error);
                            }
                        });
                    }, 1200);
                });
            });

            // Clear search input on clear button click
            const clearBtn = document.querySelector('.search-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    const input = document.querySelector('.search-input');
                    if (input) input.value = '';
                });
            }

            // Automatically load "All recent tickets" on page load
            const defaultSection = document.querySelector('li[data-section="recent"]');
            if (defaultSection) {
                defaultSection.click();
            }
        });

        // Function to load and update ticket counts
        function loadTicketCounts() {
            $.ajax({
                url: 'index.php?r=support/getcounts',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const counts = response.counts;

                        // Update badge counts
                        updateBadge('to-handle', counts.to_handle);
                        updateBadge('my-open', counts.my_open);
                        updateBadge('last-7-days', counts.last_7_days);
                        updateBadge('status-open', counts.open);
                        updateBadge('status-pending', counts.pending);
                        updateBadge('status-hold', counts.on_hold);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load ticket counts:', error);
                }
            });
        }

        // Helper function to update badge
        function updateBadge(section, count) {
            const item = document.querySelector(`li[data-section="${section}"]`);
            if (item) {
                let badge = item.querySelector('.badge');
                if (count > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'badge';
                        item.appendChild(badge);
                    }
                    badge.textContent = count;
                } else if (badge) {
                    badge.remove();
                }
            }
        }
    </script>
    <script>
        const button = document.getElementById('new-ticket-button');
        let isDragging = false,
            offsetX = 0,
            offsetY = 0;
        button.addEventListener('mousedown', function(e) {
            isDragging = true;
            offsetX = e.clientX - button.offsetLeft;
            offsetY = e.clientY - button.offsetTop;
            button.style.transition = 'none';
        });
        document.addEventListener('mousemove', function(e) {
            if (isDragging) {
                button.style.left = (e.clientX - offsetX) + 'px';
                button.style.top = (e.clientY - offsetY) + 'px';
            }
        });
        document.addEventListener('mouseup', function() {
            isDragging = false;
            button.style.transition = 'all 0.2s';
        })
        button.addEventListener('click', function() {
            const contentArea = $('#ticket-content-area');

            // Loading shimmer
            contentArea.html(`
          <h2 class="shimmer large"></h2>
          <div class="shimmer"></div> 
          <div class="shimmer medium"></div>
        `);

            // Simulate delay then load content
            setTimeout(() => {
                $.ajax({
                    url: 'index.php?r=support/loadsection',
                    method: 'GET',
                    data: {
                        section: 'create-ticket'
                    },
                    success: function(response) {
                        contentArea.html(response);
                    },
                    error: function(xhr, status, error) {
                        contentArea.html('<h2>Error</h2><p>Unable to load section.</p>');
                        console.error(error);
                    }
                });
            }, 1200);
        });
    </script>


</body>

</html>
<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?= Html::encode($this->title ? $this->title . ' - ' : '') ?>SuperManagementSystem</title>
    <meta name="description"
        content="Comprehensive School Management System - Manage students, staff, fees, exams, and more">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- jQuery (load early for inline scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <!-- Chosen jQuery Plugin CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

    <!-- Custom Ace Admin Theme Styles -->
    <style>
    :root {
        /* Ace Admin Color Scheme */
        --ace-primary: #438EB9;
        --ace-primary-dark: #2E7CB5;
        --ace-primary-darker: #1B4E8C;
        --ace-secondary: #6FB3E0;
        --ace-success: #87CEEB;
        --ace-info: #4A90E2;
        --ace-warning: #F5A623;
        --ace-danger: #E74C3C;
        --ace-dark: #2C3E50;
        --ace-light: #ECF0F1;
        --ace-white: #FFFFFF;
        --ace-gray: #95A5A6;
        --ace-gray-light: #BDC3C7;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        line-height: 1.6;
        color: var(--ace-dark);
        background: var(--ace-white);
        overflow-x: hidden;
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Ace Admin styled buttons */
    .btn-ace-primary {
        background: linear-gradient(135deg, var(--ace-primary) 0%, var(--ace-primary-dark) 100%);
        border: none;
        color: var(--ace-white);
        padding: 12px 30px;
        font-weight: 600;
        font-size: 16px;
        border-radius: 5px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(67, 142, 185, 0.3);
    }

    .btn-ace-primary:hover {
        background: linear-gradient(135deg, var(--ace-primary-dark) 0%, var(--ace-primary-darker) 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 142, 185, 0.4);
        color: var(--ace-white);
    }

    .btn-ace-secondary {
        background: var(--ace-white);
        border: 2px solid var(--ace-primary);
        color: var(--ace-primary);
        padding: 12px 30px;
        font-weight: 600;
        font-size: 16px;
        border-radius: 5px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-ace-secondary:hover {
        background: var(--ace-primary);
        color: var(--ace-white);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 142, 185, 0.3);
    }

    /* Section styling */
    .section {
        padding: 80px 0;
    }

    .section-title {
        font-size: 36px;
        font-weight: 700;
        color: var(--ace-primary-dark);
        margin-bottom: 15px;
        text-align: center;
    }

    .section-subtitle {
        font-size: 18px;
        color: var(--ace-gray);
        text-align: center;
        margin-bottom: 50px;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Card styling with Ace Admin theme */
    .ace-card {
        background: var(--ace-white);
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        padding: 30px;
        transition: all 0.3s ease;
        height: 100%;
        border-top: 3px solid var(--ace-primary);
    }

    .ace-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
    }

    .ace-card-icon {
        font-size: 48px;
        color: var(--ace-primary);
        margin-bottom: 20px;
    }

    .ace-card-title {
        font-size: 22px;
        font-weight: 600;
        color: var(--ace-primary-dark);
        margin-bottom: 15px;
    }

    .ace-card-text {
        color: var(--ace-gray);
        line-height: 1.8;
    }

    /* Gradient backgrounds */
    .bg-ace-gradient {
        background: linear-gradient(135deg, var(--ace-primary) 0%, var(--ace-primary-dark) 50%, var(--ace-primary-darker) 100%);
    }

    .bg-ace-light {
        background: var(--ace-light);
    }

    /* Text colors */
    .text-ace-primary {
        color: var(--ace-primary) !important;
    }

    .text-ace-dark {
        color: var(--ace-dark) !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .section {
            padding: 50px 0;
        }

        .section-title {
            font-size: 28px;
        }

        .section-subtitle {
            font-size: 16px;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Navigation */
    .navbar-ace {
        background: var(--ace-white);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 15px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar-ace .navbar-brand {
        font-size: 24px;
        font-weight: 700;
        color: var(--ace-primary-dark) !important;
    }

    .navbar-ace .nav-link {
        color: var(--ace-dark) !important;
        font-weight: 500;
        margin: 0 10px;
        transition: color 0.3s ease;
    }

    .navbar-ace .nav-link:hover {
        color: var(--ace-primary) !important;
    }

    .navbar-ace .navbar-toggler {
        border: 2px solid var(--ace-primary);
        padding: 0.25rem 0.5rem;
    }

    .navbar-ace .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 142, 185, 0.25);
    }

    .navbar-ace .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2844, 62, 80, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    </style>

    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <?= $content ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tooltip polyfill if not available -->
    <script>
    if (typeof jQuery !== 'undefined' && !jQuery.fn.tooltip) {
        jQuery.fn.tooltip = function() { return this; };
    }
    </script>

    <!-- Custom Scripts -->
    <script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Fade in animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.ace-card').forEach(card => {
            observer.observe(card);
        });
    });
    </script>

    <!-- Payment Invoice Modal -->
    <?php if (Yii::$app->session->get('pending_invoice_info')): ?>
        <?php echo $this->render('payment_modal'); ?>
    <?php endif; ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
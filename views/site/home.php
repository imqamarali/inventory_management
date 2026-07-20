<?php

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'SuperManagementSystem - Comprehensive School Management Solution';

?>

<!-- Hero Section with Animated Background -->
<section id="home" class="hero-section">
    <div class="hero-animated-bg"></div>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>
    <!-- Floating Modules & Features in Background -->
    <div class="floating-modules-features-top">
        <div class="floating-mini-card module-mini card-bg-1">
            <i class="fas fa-user-graduate"></i>
            <span>Student Portal</span>
        </div>
        <div class="floating-mini-card module-mini card-bg-2">
            <i class="fas fa-briefcase"></i>
            <span>HR</span>
        </div>
        <div class="floating-mini-card module-mini card-bg-3">
            <i class="fas fa-calendar-alt"></i>
            <span>Calendar</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-4">
            <i class="fas fa-money-bill-wave"></i>
            <span>Fee Mgmt</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-5">
            <i class="fas fa-clipboard-list"></i>
            <span>Exams</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-6">
            <i class="fas fa-calendar-check"></i>
            <span>Attendance</span>
        </div>
        <div class="floating-mini-card module-mini card-bg-7">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
        </div>
        <div class="floating-mini-card module-mini card-bg-8">
            <i class="fas fa-file-alt"></i>
            <span>Docs</span>
        </div>
        <div class="floating-mini-card module-mini card-bg-9">
            <i class="fas fa-users-cog"></i>
            <span>CRM</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-10">
            <i class="fas fa-book"></i>
            <span>Academic</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-11">
            <i class="fas fa-video"></i>
            <span>Classes</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-12">
            <i class="fas fa-headset"></i>
            <span>Support</span>
        </div>
        <div class="floating-mini-card feature-mini card-bg-13">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
        </div>
    </div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="fas fa-star"></i> Trusted by 1000+ Schools
                </div>
                <h1 class="hero-title">
                    <span class="title-line-1">Complete School</span>
                    <span class="title-line-2 gradient-text">Management System</span>
                </h1>
                <p class="hero-subtitle">
                    Streamline your entire educational institution with our comprehensive management platform.
                    Manage students, staff, fees, exams, attendance, and more from one centralized system.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number" data-count="1000">0</div>
                        <div class="stat-label">Schools</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-count="50000">0</div>
                        <div class="stat-label">Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" data-count="99">0</div>
                        <div class="stat-label">% Uptime</div>
                    </div>
                </div>
                <div class="hero-buttons">
                    <a href="<?= Url::to(['site/login']) ?>" class="btn btn-hero-primary">
                        <i class="fas fa-rocket"></i> Get Started Free
                    </a>
                    <a href="#features" class="btn btn-hero-secondary">
                        <i class="fas fa-play-circle"></i> Watch Demo
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-preview-container">
                    <div class="dashboard-carousel">
                        <!-- Main Dashboard -->
                        <div class="dashboard-slide active">
                            <div class="preview-welcome-banner">
                                <div>
                                    <h4>Welcome, Admin! 👋</h4>
                                    <p>SuperManagementSystem • <?= date('M j, Y') ?></p>
                                </div>
                                <div class="preview-welcome-stats">
                                    <div class="preview-welcome-stat">
                                        <span class="preview-welcome-num">1,234</span>
                                        <span class="preview-welcome-label">Students</span>
                                    </div>
                                    <div class="preview-welcome-stat">
                                        <span class="preview-welcome-num">56</span>
                                        <span class="preview-welcome-label">Teachers</span>
                                    </div>
                                    <div class="preview-welcome-stat">
                                        <span class="preview-welcome-num">24</span>
                                        <span class="preview-welcome-label">Classes</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-stats-grid">
                                <div class="preview-stat-card s-blue">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Students</div>
                                        <div class="preview-stat-value">1,234</div>
                                        <div class="preview-stat-subtitle">Active: 1,200 • M: 650 • F: 550</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-users"></i></div>
                                </div>
                                <div class="preview-stat-card s-green">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Teachers</div>
                                        <div class="preview-stat-value">56</div>
                                        <div class="preview-stat-subtitle">Faculty Members</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                                </div>
                                <div class="preview-stat-card s-teal">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">New Admissions</div>
                                        <div class="preview-stat-value">45</div>
                                        <div class="preview-stat-subtitle">This Month</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-user-plus"></i></div>
                                </div>
                                <div class="preview-stat-card s-orange">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Today's Collection</div>
                                        <div class="preview-stat-value">45,678</div>
                                        <div class="preview-stat-subtitle">Fee Collected</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-money"></i></div>
                                </div>
                                <div class="preview-stat-card s-blue">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Monthly Total</div>
                                        <div class="preview-stat-value">1,234,567</div>
                                        <div class="preview-stat-subtitle">This Month</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-bar-chart"></i></div>
                                </div>
                                <div class="preview-stat-card s-red">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Outstanding</div>
                                        <div class="preview-stat-value">234,567</div>
                                        <div class="preview-stat-subtitle">Pending • Overdue</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-warning"></i></div>
                                </div>
                                <div class="preview-stat-card s-purple">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Exams</div>
                                        <div class="preview-stat-value">12</div>
                                        <div class="preview-stat-subtitle">Total</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-pencil-square-o"></i></div>
                                </div>
                                <div class="preview-stat-card s-green">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Classes</div>
                                        <div class="preview-stat-value">24</div>
                                        <div class="preview-stat-subtitle">12 Sections</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-building"></i></div>
                                </div>
                                <div class="preview-stat-card s-teal">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Meetings</div>
                                        <div class="preview-stat-value">8</div>
                                        <div class="preview-stat-subtitle">5 Upcoming</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-video-camera"></i></div>
                                </div>
                                <div class="preview-stat-card s-orange">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Tickets</div>
                                        <div class="preview-stat-value">12</div>
                                        <div class="preview-stat-subtitle">3 Open</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-ticket"></i></div>
                                </div>
                                <div class="preview-stat-card s-blue">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Subjects</div>
                                        <div class="preview-stat-value">45</div>
                                        <div class="preview-stat-subtitle">Available</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-book"></i></div>
                                </div>
                                <div class="preview-stat-card s-purple">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Documents</div>
                                        <div class="preview-stat-value">234</div>
                                        <div class="preview-stat-subtitle">Uploaded</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-folder"></i></div>
                                </div>
                                <div class="preview-stat-card s-green">
                                    <div class="preview-stat-info">
                                        <div class="preview-stat-title">Attendance</div>
                                        <div class="preview-stat-value">1,156</div>
                                        <div class="preview-stat-subtitle">Present Today</div>
                                    </div>
                                    <div class="preview-stat-icon"><i class="fa fa-check-circle"></i></div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="preview-widget">
                                <div class="preview-widget-header">
                                    <h5 class="preview-widget-title"><i class="fa fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="preview-widget-body">
                                    <div class="preview-quick-actions">
                                        <a href="#" class="preview-quick-btn qb-blue"><i
                                                class="fa fa-users"></i><span>Students</span></a>
                                        <a href="#" class="preview-quick-btn qb-green"><i
                                                class="fa fa-chalkboard-teacher"></i><span>Teachers</span></a>
                                        <a href="#" class="preview-quick-btn qb-orange"><i
                                                class="fa fa-money"></i><span>Fees</span></a>
                                        <a href="#" class="preview-quick-btn qb-red"><i
                                                class="fa fa-calendar"></i><span>Attendance</span></a>
                                        <a href="#" class="preview-quick-btn qb-purple"><i
                                                class="fa fa-book"></i><span>Classes</span></a>
                                        <a href="#" class="preview-quick-btn qb-teal"><i
                                                class="fa fa-file"></i><span>Reports</span></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Fee Overview & System Info Row -->
                            <div class="preview-widgets-row">
                                <!-- Fee Overview -->
                                <div class="preview-widget">
                                    <div class="preview-widget-header">
                                        <h5 class="preview-widget-title"><i class="fa fa-wallet"></i> Fee Overview</h5>
                                    </div>
                                    <div class="preview-widget-body">
                                        <div class="preview-fee-list">
                                            <div class="preview-fee-item"><span
                                                    class="preview-badge badge-warning">Pending</span><span>45</span>
                                            </div>
                                            <div class="preview-fee-item"><span
                                                    class="preview-badge badge-info">Partial</span><span>12</span></div>
                                            <div class="preview-fee-item"><span
                                                    class="preview-badge badge-danger">Overdue</span><span>8</span>
                                            </div>
                                            <div class="preview-fee-item"><span
                                                    class="preview-badge badge-success">Paid</span><span>234</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- System Info -->
                                <div class="preview-widget">
                                    <div class="preview-widget-header">
                                        <h5 class="preview-widget-title"><i class="fa fa-info-circle"></i> System Info
                                        </h5>
                                    </div>
                                    <div class="preview-widget-body">
                                        <div class="preview-info-chips">
                                            <span class="preview-info-chip">School: SMS Demo</span>
                                            <span class="preview-info-chip">Session: 2024</span>
                                            <span class="preview-info-chip">Role: Admin</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="scroll-indicator">
                <div class="mouse">
                    <div class="wheel"></div>
                </div>
                <div class="arrow">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
</section>


<style>
/* ============================================
   ENHANCED STYLES FOR COOL MODERN DESIGN
   ============================================ */

/* Navigation Enhancements - Flat Modern Design */
.navbar-ace.fixed-top {
    background: linear-gradient(135deg, #2E7CB5 0%, #438EB9 100%) !important;
    backdrop-filter: none;
    box-shadow: 0 2px 10px rgba(46, 124, 181, 0.15);
    transition: all 0.3s ease;
    padding: 12px 0;
    min-height: 60px;
}

.navbar-ace.fixed-top.scrolled {
    background: linear-gradient(135deg, #2E7CB5 0%, #438EB9 100%) !important;
    box-shadow: 0 4px 20px rgba(46, 124, 181, 0.2);
    padding: 10px 0;
}

.navbar-ace .container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.navbar-ace .navbar-nav {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    margin: 0;
}

.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    transition: all 0.3s ease;
    color: #2E7CB5 !important;
    display: flex;
    align-items: center;
    gap: 10px;
}

.navbar-brand i {
    font-size: 1.8rem;
    color: #438EB9;
}

.brand-text {
    background: linear-gradient(135deg, #2E7CB5, #438EB9);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.navbar-brand:hover {
    transform: translateY(-2px);
}

.navbar-brand:hover i {
    transform: rotate(15deg);
    transition: transform 0.3s ease;
}

.nav-link {
    position: relative;
    transition: all 0.3s ease;
    color: #2C3E50 !important;
    font-weight: 500;
    padding: 8px 15px !important;
    margin: 0 5px;
    border-radius: 8px;
}

.nav-link:hover {
    color: #438EB9 !important;
    background: rgba(67, 142, 185, 0.08);
    transform: translateY(-1px);
}

.nav-link::after {
    display: none;
}

/* Flat button styling */
.nav-link .btn {
    margin: 0;
    padding: 10px 25px;
    border-radius: 8px;
    background: linear-gradient(135deg, #438EB9, #2E7CB5);
    border: none;
    transition: all 0.3s ease;
}

.nav-link .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(67, 142, 185, 0.3);
}

/* Cool Login Button */
.btn-login-cool {
    background: rgba(255, 255, 255, 0.95);
    color: #2E7CB5 !important;
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-login-cool:hover {
    background: #ffffff;
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    color: #1B4E8C !important;
    border-color: rgba(255, 255, 255, 0.5);
}

.btn-login-cool i {
    transition: transform 0.3s ease;
}

.btn-login-cool:hover i {
    transform: translateX(3px);
}

.pulse-btn {
    position: relative;
    overflow: hidden;
}

.pulse-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.pulse-btn:hover::before {
    width: 300px;
    height: 300px;
}

/* Navbar Toggler - Flat Design */
.navbar-toggler {
    border: 2px solid #438EB9;
    border-radius: 6px;
    padding: 6px 10px;
    transition: all 0.3s ease;
}

.navbar-toggler:focus {
    box-shadow: none;
    border-color: #2E7CB5;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2846, 124, 181, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    width: 24px;
    height: 24px;
}

.navbar-toggler:hover {
    background: rgba(67, 142, 185, 0.1);
    border-color: #2E7CB5;
}

/* Hero Section Enhancements */
.hero-section {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(135deg, #2E7CB5 0%, #438EB9 50%, #1B4E8C 100%);
    padding-top: 80px;
}

.hero-animated-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
    animation: backgroundShift 20s ease infinite;
}

@keyframes backgroundShift {

    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    50% {
        transform: translate(-20px, -20px) scale(1.1);
    }
}

.floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 15s infinite ease-in-out;
}

.shape-1 {
    width: 200px;
    height: 200px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 150px;
    height: 150px;
    top: 60%;
    right: 10%;
    animation-delay: 2s;
}

.shape-3 {
    width: 100px;
    height: 100px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

.shape-4 {
    width: 120px;
    height: 120px;
    top: 30%;
    right: 30%;
    animation-delay: 6s;
}

@keyframes float {

    0%,
    100% {
        transform: translate(0, 0) rotate(0deg);
    }

    33% {
        transform: translate(30px, -30px) rotate(120deg);
    }

    66% {
        transform: translate(-20px, 20px) rotate(240deg);
    }
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 8px 20px;
    border-radius: 50px;
    color: white;
    font-size: 0.9rem;
    margin-bottom: 20px;
    animation: fadeInDown 0.8s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 20px;
    color: white;
    animation: fadeInUp 0.8s ease 0.2s both;
}

.title-line-1 {
    display: block;
    margin-bottom: 10px;
}

.title-line-2 {
    display: block;
}

.gradient-text {
    background: linear-gradient(90deg, #FFF 0%, #E3F2FD 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 30px;
    line-height: 1.8;
    animation: fadeInUp 0.8s ease 0.4s both;
}

.hero-stats {
    display: flex;
    gap: 30px;
    margin-bottom: 40px;
    animation: fadeInUp 0.8s ease 0.6s both;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    display: block;
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    margin-top: 5px;
}

.hero-buttons {
    display: flex;
    gap: 15px;
    animation: fadeInUp 0.8s ease 0.8s both;
}

.btn-hero-primary {
    background: white;
    color: #438EB9;
    padding: 15px 35px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    color: #2E7CB5;
}

.btn-hero-secondary {
    background: transparent;
    color: white;
    padding: 15px 35px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    border: 2px solid white;
    transition: all 0.3s ease;
}

.btn-hero-secondary:hover {
    background: white;
    color: #438EB9;
    transform: translateY(-3px);
}

.hero-visual {
    position: relative;
    height: 500px;
    animation: fadeInRight 1s ease 0.5s both;
}

.hero-main-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 200px;
    color: rgba(255, 255, 255, 0.3);
    animation: pulseIcon 3s infinite;
}

/* Dashboard Preview Styles */
.hero-dashboard-preview {
    position: relative;
    animation: fadeInRight 1s ease 0.5s both;
}

.dashboard-preview-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    max-height: 600px;
    overflow: hidden;
    position: relative;
}

.dashboard-preview-container::-webkit-scrollbar {
    width: 4px;
}

.dashboard-preview-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.dashboard-preview-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.dashboard-preview-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.dashboard-carousel {
    position: relative;
    width: 100%;
    animation: scrollContent 25s ease-in-out infinite;
}

.dashboard-slide {
    width: 100%;
    animation: slideInFromLeft 1.5s ease-out;
}

@keyframes scrollContent {

    0%,
    100% {
        transform: translateX(0);
    }

    50% {
        transform: translateX(-15px);
    }
}

@keyframes slideInFromLeft {
    0% {
        opacity: 0;
        transform: translateX(-30px);
    }

    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

.preview-welcome-banner {
    background: linear-gradient(135deg, #438EB9f5 0%, #2E7CB5dd 100%);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-welcome-banner h4 {
    margin: 0;
    font-size: 12px;
    font-weight: 600;
}

.preview-welcome-banner p {
    margin: 2px 0 0 0;
    font-size: 8px;
    opacity: 0.9;
}

.preview-welcome-stats {
    display: flex;
    gap: 15px;
    align-items: center;
}

.preview-welcome-stat {
    text-align: center;
}

.preview-welcome-num {
    font-size: 14px;
    font-weight: 700;
    display: block;
}

.preview-welcome-label {
    font-size: 7px;
    opacity: 0.9;
    text-transform: uppercase;
}

.preview-stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 6px;
}

.preview-stat-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 6px;
    padding: 6px 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    border-left: 2px solid;
    min-height: 60px;
}

.preview-stat-card.s-blue {
    border-left-color: #4dabf7;
}

.preview-stat-card.s-green {
    border-left-color: #51cf66;
}

.preview-stat-card.s-orange {
    border-left-color: #ffa726;
}

.preview-stat-card.s-purple {
    border-left-color: #b197fc;
}

.preview-stat-card.s-red {
    border-left-color: #ff6b6b;
}

.preview-stat-card.s-teal {
    border-left-color: #20c997;
}

.preview-stat-info {
    flex: 1;
    width: 100%;
}

.preview-stat-title {
    font-size: 6px;
    color: #6c757d;
    text-transform: uppercase;
    margin-bottom: 2px;
    font-weight: 500;
    line-height: 1.2;
}

.preview-stat-value {
    font-size: 11px;
    font-weight: 700;
    color: #2d3748;
    line-height: 1.1;
    margin: 2px 0;
}

.preview-stat-subtitle {
    font-size: 5px;
    color: #6c757d;
    margin-top: 1px;
    line-height: 1.2;
}

.preview-stat-icon {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    position: absolute;
    top: 6px;
    right: 6px;
}

.preview-stat-card {
    position: relative;
}

.preview-stat-card.s-blue .preview-stat-icon {
    background: rgba(77, 171, 247, 0.1);
    color: #4dabf7;
}

.preview-stat-card.s-green .preview-stat-icon {
    background: rgba(81, 207, 102, 0.1);
    color: #51cf66;
}

.preview-stat-card.s-orange .preview-stat-icon {
    background: rgba(255, 167, 38, 0.1);
    color: #ffa726;
}

.preview-stat-card.s-purple .preview-stat-icon {
    background: rgba(177, 151, 252, 0.1);
    color: #b197fc;
}

.preview-stat-card.s-red .preview-stat-icon {
    background: rgba(255, 107, 107, 0.1);
    color: #ff6b6b;
}

.preview-stat-card.s-teal .preview-stat-icon {
    background: rgba(32, 201, 151, 0.1);
    color: #20c997;
}

/* Preview Widget Styles */
.preview-widgets-row {
    display: flex;
    gap: 6px;
    margin-top: 6px;
}

.preview-widgets-row .preview-widget {
    flex: 1;
    margin-top: 0;
}

.preview-widget {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-top: 6px;
}

.preview-widget-header {
    padding: 6px 10px;
    border-bottom: 1px solid #e9ecef;
}

.preview-widget-title {
    font-size: 9px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.preview-widget-body {
    padding: 6px 10px;
}

.preview-quick-actions {
    display: flex;
    flex-wrap: nowrap;
    gap: 4px;
    overflow-x: auto;
    width: 100%;
}

.preview-quick-actions::-webkit-scrollbar {
    height: 3px;
}

.preview-quick-actions::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.preview-quick-actions::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.preview-quick-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 5px 4px;
    border-radius: 4px;
    text-align: center;
    text-decoration: none;
    font-size: 7px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    flex: 1 0 auto;
    min-width: fit-content;
    white-space: nowrap;
}

.preview-quick-btn i {
    font-size: 10px;
}

.preview-quick-btn span {
    font-size: 6px;
}

.preview-quick-btn.qb-blue {
    background: linear-gradient(135deg, #4dabf7, #339af0);
}

.preview-quick-btn.qb-green {
    background: linear-gradient(135deg, #51cf66, #37b24d);
}

.preview-quick-btn.qb-orange {
    background: linear-gradient(135deg, #ffa726, #fb8c00);
}

.preview-quick-btn.qb-red {
    background: linear-gradient(135deg, #ff6b6b, #c92a2a);
}

.preview-quick-btn.qb-purple {
    background: linear-gradient(135deg, #b197fc, #9775fa);
}

.preview-quick-btn.qb-teal {
    background: linear-gradient(135deg, #20c997, #12b886);
}

/* Preview Table Styles */
.preview-table {
    width: 100%;
    font-size: 8px;
    border-collapse: collapse;
}

.preview-table thead th {
    background: #f8f9fa;
    padding: 4px 6px;
    font-weight: 600;
    font-size: 7px;
    text-transform: uppercase;
    color: #6c757d;
    border-bottom: 1px solid #e9ecef;
    text-align: left;
}

.preview-table tbody td {
    padding: 4px 6px;
    border-bottom: 1px solid #f8f9fa;
    font-size: 8px;
    color: #2d3748;
}

.preview-table tbody tr:hover {
    background: #f8f9fa;
}

/* Preview Fee List */
.preview-fee-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5px;
}

.preview-fee-item {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 5px 8px;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 7px;
}

.preview-fee-item span:last-child {
    font-weight: 700;
    font-size: 10px;
    color: #2d3748;
    margin-top: 2px;
}

.preview-badge {
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 7px;
    font-weight: 600;
    text-transform: uppercase;
}

.preview-badge.badge-warning {
    background: #fff3bf;
    color: #f08c00;
}

.preview-badge.badge-info {
    background: #d0ebff;
    color: #1864ab;
}

.preview-badge.badge-danger {
    background: #ffe3e3;
    color: #c92a2a;
}

.preview-badge.badge-success {
    background: #d3f9d8;
    color: #2b8a3e;
}

/* Preview Info Chips */
.preview-info-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.preview-info-chip {
    display: inline-block;
    padding: 4px 8px;
    background: linear-gradient(135deg, #438EB9, #2E7CB5);
    color: white;
    border-radius: 12px;
    font-size: 7px;
    font-weight: 500;
    white-space: nowrap;
}

/* Preview List Styles */
.preview-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.preview-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    border-bottom: 1px solid #f1f3f5;
    font-size: 8px;
}

.preview-list-item:last-child {
    border-bottom: none;
}

.preview-list-item:hover {
    background: #f8f9fa;
    margin: 0 -8px;
    padding: 5px 8px;
    border-radius: 4px;
}

@keyframes pulseIcon {

    0%,
    100% {
        transform: translate(-50%, -50%) scale(1);
    }

    50% {
        transform: translate(-50%, -50%) scale(1.1);
    }
}

.floating-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 15px 25px;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    color: #438EB9;
    font-weight: 600;
    animation: floatCard 6s infinite ease-in-out;
}

.card-1 {
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.card-2 {
    top: 50%;
    right: 10%;
    animation-delay: 2s;
}

.card-3 {
    bottom: 20%;
    left: 15%;
    animation-delay: 4s;
}

@keyframes floatCard {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

/* Floating Modules & Features in Background */
.floating-modules-features-top {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 1;
    pointer-events: none;
    overflow: hidden;
}

.floating-mini-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(8px);
    padding: 8px 14px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.4);
    white-space: nowrap;
    opacity: 1;
}

.card-bg-1 {
    top: 8%;
    left: 5%;
}

.card-bg-2 {
    top: 15%;
    right: 8%;
}

.card-bg-3 {
    top: 25%;
    left: 12%;
}

.card-bg-4 {
    top: 35%;
    right: 15%;
}

.card-bg-5 {
    top: 45%;
    left: 8%;
}

.card-bg-6 {
    top: 55%;
    right: 12%;
}

.card-bg-7 {
    top: 65%;
    left: 15%;
}

.card-bg-8 {
    top: 20%;
    left: 25%;
}

.card-bg-9 {
    top: 50%;
    right: 25%;
}

.card-bg-10 {
    top: 70%;
    right: 8%;
}

.card-bg-11 {
    top: 30%;
    left: 35%;
}

.card-bg-12 {
    top: 60%;
    left: 30%;
}

.card-bg-13 {
    top: 75%;
    right: 18%;
}

.floating-mini-card i {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.3);
}

.floating-mini-card span {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.3);
}

.module-mini {
    border-left: 3px solid rgba(111, 179, 224, 0.2);
}

.feature-mini {
    border-left: 3px solid rgba(67, 142, 185, 0.2);
}

@keyframes fadeInUpCard {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes floatCard {

    0%,
    100% {
        transform: translate(0, 0) rotate(0deg);
    }

    33% {
        transform: translate(20px, -20px) rotate(2deg);
    }

    66% {
        transform: translate(-15px, 15px) rotate(-2deg);
    }
}

.scroll-indicator {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    animation: fadeInUp 1s ease 1s both;
}

.mouse {
    width: 24px;
    height: 40px;
    border: 2px solid white;
    border-radius: 15px;
    margin: 0 auto 10px;
    position: relative;
}

.wheel {
    width: 4px;
    height: 8px;
    background: white;
    border-radius: 2px;
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    animation: scroll 2s infinite;
}

@keyframes scroll {
    0% {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    100% {
        opacity: 0;
        transform: translateX(-50%) translateY(15px);
    }
}

/* Section Headers */
.section-header {
    text-align: center;
    margin-bottom: 60px;
}

.section-badge {
    display: inline-block;
    background: linear-gradient(135deg, #438EB9, #6FB3E0);
    color: white;
    padding: 8px 25px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
}

/* Feature Cards Enhancements */
.feature-card {
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(67, 142, 185, 0.1), transparent);
    transition: left 0.5s;
}

.feature-card:hover::before {
    left: 100%;
}

.feature-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 60px rgba(67, 142, 185, 0.3);
}

.card-icon-wrapper {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #438EB9, #6FB3E0);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.feature-card:hover .card-icon-wrapper {
    transform: rotateY(360deg) scale(1.1);
}

.card-icon-wrapper i {
    font-size: 30px;
    color: white;
}

.card-link {
    color: #438EB9;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 15px;
    transition: all 0.3s ease;
}

.card-link:hover {
    gap: 10px;
    color: #2E7CB5;
}

/* Compact Features Section */
.features-compact-section {
    padding: 50px 0;
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.features-header-compact {
    text-align: center;
    margin-bottom: 40px;
}

.features-badge-compact {
    display: inline-block;
    background: linear-gradient(135deg, #438EB9, #6FB3E0);
    color: white;
    padding: 6px 20px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}

.features-title-compact {
    font-size: 2rem;
    font-weight: 700;
    color: #2E7CB5;
    margin-bottom: 10px;
}

.features-subtitle-compact {
    font-size: 0.95rem;
    color: #95A5A6;
    max-width: 600px;
    margin: 0 auto;
}

.features-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-item-compact {
    background: white;
    padding: 25px 20px;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.feature-item-compact::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(67, 142, 185, 0.1), transparent);
    transition: left 0.5s;
}

.feature-item-compact:hover::before {
    left: 100%;
}

.feature-item-compact:hover {
    transform: translateY(-5px);
    border-color: #438EB9;
    box-shadow: 0 10px 30px rgba(67, 142, 185, 0.2);
}

.feature-icon-compact {
    width: 55px;
    height: 55px;
    margin: 0 auto 15px;
    background: linear-gradient(135deg, #438EB9, #6FB3E0);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}

.feature-icon-compact i {
    font-size: 24px;
    color: white;
    z-index: 2;
    position: relative;
}

.feature-item-compact:hover .feature-icon-compact {
    transform: rotateY(360deg) scale(1.1);
}

.feature-title-compact {
    font-size: 1rem;
    font-weight: 600;
    color: #2E7CB5;
    margin-bottom: 8px;
    line-height: 1.3;
}

.feature-desc-compact {
    font-size: 0.85rem;
    color: #95A5A6;
    line-height: 1.5;
    margin: 0;
}

/* Module Cards */
.module-card {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.module-card::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(67, 142, 185, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s;
}

.module-card:hover::after {
    opacity: 1;
}

.module-card:hover {
    transform: translateY(-10px);
    border-color: #438EB9;
    box-shadow: 0 20px 60px rgba(67, 142, 185, 0.2);
}

.module-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #438EB9, #6FB3E0);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.module-icon i {
    font-size: 35px;
    color: white;
}

.module-card:hover .module-icon {
    transform: rotate(360deg) scale(1.1);
}

.module-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2E7CB5;
    margin-bottom: 10px;
}

.module-text {
    color: #95A5A6;
    font-size: 0.9rem;
}

/* Benefits Section */
.benefit-item {
    display: flex;
    gap: 20px;
    padding: 30px;
    background: white;
    border-radius: 15px;
    transition: all 0.3s ease;
    border-left: 4px solid #438EB9;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.benefit-item:hover {
    transform: translateX(10px);
    box-shadow: 0 10px 30px rgba(67, 142, 185, 0.2);
}

.benefit-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #438EB9, #6FB3E0);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.benefit-icon i {
    color: white;
    font-size: 20px;
}

.benefit-content h4 {
    color: #2E7CB5;
    font-weight: 600;
    margin-bottom: 10px;
}

/* Stats Section */
.stats-section {
    position: relative;
    overflow: hidden;
    padding: 80px 0;
}

.stats-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');
    opacity: 0.3;
}

.stat-box {
    position: relative;
    z-index: 2;
    padding: 30px;
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.9;
}

.stat-value {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

/* CTA Section */
.cta-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #F8F9FA 0%, #E9ECEF 100%);
}

.cta-box {
    background: linear-gradient(135deg, #438EB9, #2E7CB5);
    border-radius: 30px;
    padding: 60px;
    box-shadow: 0 20px 60px rgba(67, 142, 185, 0.3);
    position: relative;
    overflow: hidden;
}

.cta-box::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.cta-title {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    position: relative;
    z-index: 2;
}

.cta-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    position: relative;
    z-index: 2;
}

.btn-cta-primary {
    background: white;
    color: #438EB9;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 2;
}

.btn-cta-primary:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

/* Footer */
.footer-modern {
    background: #1B1B2F;
    color: white;
    padding: 60px 0 20px;
}

.footer-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: white;
}

.footer-text {
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.8;
    margin-bottom: 20px;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-link:hover {
    background: #438EB9;
    transform: translateY(-3px);
    color: white;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #6FB3E0;
    padding-left: 5px;
}

.footer-contact {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-contact li {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 15px;
    color: rgba(255, 255, 255, 0.7);
}

.footer-contact i {
    color: #438EB9;
    margin-top: 5px;
}

.footer-bottom {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-copyright {
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
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

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar-ace {
        padding: 10px 0;
    }

    .btn-login-cool {
        padding: 10px 25px;
        font-size: 0.9rem;
    }

    .features-compact-section {
        padding: 40px 0;
    }

    .features-grid-compact {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .feature-item-compact {
        padding: 20px 15px;
    }

    .feature-icon-compact {
        width: 45px;
        height: 45px;
        margin-bottom: 12px;
    }

    .feature-icon-compact i {
        font-size: 20px;
    }

    .feature-title-compact {
        font-size: 0.9rem;
    }

    .feature-desc-compact {
        font-size: 0.8rem;
    }

    .features-title-compact {
        font-size: 1.5rem;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .hero-stats {
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-buttons {
        flex-direction: column;
    }

    .stat-value {
        font-size: 2rem;
    }

    .dashboard-preview-container {
        padding: 12px;
        max-height: 400px;
    }

    .preview-stats-grid {
        grid-template-columns: 1fr;
        gap: 6px;
    }

    .preview-stat-card {
        padding: 8px 10px;
    }

    .preview-stat-value {
        font-size: 16px;
    }

    .cta-title {
        font-size: 1.8rem;
    }

    .cta-box {
        padding: 40px 30px;
    }

    .benefit-item {
        flex-direction: column;
        text-align: center;
    }

    .floating-mini-card {
        padding: 6px 10px;
        font-size: 0.65rem;
        border-radius: 10px;
        gap: 5px;
    }

    .floating-mini-card i {
        font-size: 0.75rem;
    }

    .floating-mini-card span {
        font-size: 0.6rem;
    }
}

/* Scroll animations */
@media (prefers-reduced-motion: no-preference) {

    .ace-card,
    .module-card,
    .benefit-item {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .ace-card.visible,
    .module-card.visible,
    .benefit-item.visible {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Counter Animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-count]');
    const speed = 200;

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(() => animateCounter(counter), 1);
        } else {
            counter.innerText = target;
        }
    };

    const observerOptions = {
        threshold: 0.5
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                if (!counter.classList.contains('counted')) {
                    counter.classList.add('counted');
                    animateCounter(counter);
                }
                observer.unobserve(counter);
            }
        });
    }, observerOptions);

    counters.forEach(counter => observer.observe(counter));

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar-ace.fixed-top');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Scroll animations
    const scrollElements = document.querySelectorAll('.ace-card, .module-card, .benefit-item');
    const elementObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });

    scrollElements.forEach(el => elementObserver.observe(el));
});
</script>
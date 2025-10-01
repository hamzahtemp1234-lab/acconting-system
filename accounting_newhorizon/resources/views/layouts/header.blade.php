<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم المحاسبية - النظام المتكامل</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #05205a;
            --secondary-color: #f5af22;
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .text-primary {
            color: var(--primary-color);
        }

        .bg-secondary {
            background-color: var(--secondary-color);
        }

        .text-secondary {
            color: var(--secondary-color);
        }

        body {
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }

        /* تحسينات للشاشات الصغيرة - التصحيح الأساسي هنا */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                height: 100vh;
                z-index: 1000;
                transform: translateX(100%);
                /* القائمة تبدأ خارج الشاشة من اليمين */
                transition: transform 0.3s ease;
                right: 0;
                /* تثبيت القائمة على اليمين */
                top: 0;
                width: 280px;
            }

            #sidebar.active {
                transform: translateX(0);
                /* عند التفعيل، تظهر القائمة */
            }

            #sidebar-toggle {
                display: block;
            }

            main {
                width: 100%;
                padding: 1rem;
            }

            header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            header h1 {
                font-size: 1.5rem;
                text-align: center;
                width: 100%;
            }

            /* تحسينات رئيسية للأزرار */
            .header-buttons {
                display: flex;
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }

            .header-buttons button {
                width: 100%;
                margin: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            /* إزالة المساحة الفارغة على اليسار */
            body {
                padding: 0;
            }

            main {
                padding: 0.5rem;
            }

            /* تحسينات إضافية للشاشات الصغيرة جداً */
            @media (max-width: 480px) {
                main {
                    padding: 0.25rem;
                }

                header h1 {
                    font-size: 1.3rem;
                }

                .header-buttons button {
                    padding: 0.75rem 0.5rem;
                    font-size: 0.9rem;
                }
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            #sidebar {
                position: relative;
                transform: translateX(0);
                height: 100vh;
                overflow-y: auto;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .header-buttons {
                display: flex;
                flex-direction: row;
                gap: 1rem;
            }
        }

        @media (min-width: 1025px) {
            #sidebar {
                position: relative;
                transform: translateX(0);
                height: 100vh;
                overflow-y: auto;
            }

            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .main-grid {
                grid-template-columns: 2fr 1fr;
            }

            .header-buttons {
                display: flex;
                flex-direction: row;
                gap: 1rem;
            }
        }

        /* زر القائمة للشاشات الصغيرة - تم التصحيح */
        #sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            /* زر القائمة في الجانب الأيمن */
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* طبقة التعتيم للشاشات الصغيرة - تم التصحيح */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #overlay.active {
            display: block;
        }

        /* تنسيقات عامة للقائمة - التصحيح هنا */
        #sidebar {
            width: 280px;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* زر إغلاق القائمة */
        #close-sidebar {
            display: none;
        }

        @media (max-width: 768px) {
            #close-sidebar {
                display: block;
            }
        }

        /* تحسينات إضافية للتصميم العربي */
        .table-header {
            text-align: right;
        }

        .table-cell {
            text-align: right;
        }

        /* تحسينات للأزرار */
        .header-buttons {
            display: flex;
            gap: 1rem;
        }

        .header-buttons button {
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .header-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* تحسينات للقائمة الجانبية */
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 1rem;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 1rem;
        }

        /* تحسينات للشاشات الصغيرة - التصحيح الأساسي هنا */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                height: 100vh;
                z-index: 1000;
                transform: translateX(100%);
                /* القائمة تبدأ خارج الشاشة من اليمين */
                transition: transform 0.3s ease;
                right: 0;
                /* تثبيت القائمة على اليمين */
                top: 0;
                width: 280px;
            }

            #sidebar.active {
                transform: translateX(0);
                /* عند التفعيل، تظهر القائمة */
            }

            #sidebar-toggle {
                display: block;
            }

            main {
                width: 100%;
                padding: 1rem;
            }

            header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            header h1 {
                font-size: 1.5rem;
                text-align: center;
                width: 100%;
            }

            /* تحسينات رئيسية للأزرار */
            .header-buttons {
                display: flex;
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }

            .header-buttons button {
                width: 100%;
                margin: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            /* إزالة المساحة الفارغة على اليسار */
            body {
                padding: 0;
            }

            main {
                padding: 0.5rem;
            }

            /* تحسينات إضافية للشاشات الصغيرة جداً */
            @media (max-width: 480px) {
                main {
                    padding: 0.25rem;
                }

                header h1 {
                    font-size: 1.3rem;
                }

                .header-buttons button {
                    padding: 0.75rem 0.5rem;
                    font-size: 0.9rem;
                }
            }

            /* تحسينات خاصة لشريط البحث */
            .search-section {
                flex-direction: column;
                gap: 1rem;
            }

            .search-section>div {
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .search-section .search-input-container {
                width: 100%;
            }

            .search-section .filter-container {
                width: 100%;
            }

            .search-section .user-count {
                width: 100%;
                text-align: center;
                margin-top: 0.5rem;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            #sidebar {
                position: relative;
                transform: translateX(0);
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .header-buttons {
                display: flex;
                flex-direction: row;
                gap: 1rem;
            }

            /* تحسينات خاصة لشريط البحث للشاشات المتوسطة */
            .search-section {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .search-section>div {
                flex: 1;
                min-width: 200px;
            }

            .search-section .user-count {
                flex-basis: 100%;
                text-align: center;
                margin-top: 0.5rem;
            }
        }

        @media (min-width: 1025px) {
            #sidebar {
                position: relative;
                transform: translateX(0);
            }

            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .main-grid {
                grid-template-columns: 2fr 1fr;
            }

            .header-buttons {
                display: flex;
                flex-direction: row;
                gap: 1rem;
            }

            /* تحسينات خاصة لشريط البحث للشاشات الكبيرة */
            .search-section {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .search-section>div {
                flex: 1;
                margin-left: 1rem;
            }

            .search-section .user-count {
                flex: 0 0 auto;
                white-space: nowrap;
            }
        }

        /* زر القائمة للشاشات الصغيرة - تم التصحيح */
        #sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            /* زر القائمة في الجانب الأيمن */
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* طبقة التعتيم للشاشات الصغيرة - تم التصحيح */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #overlay.active {
            display: block;
        }

        /* تنسيقات عامة للقائمة */
        #sidebar {
            width: 280px;
        }

        /* زر إغلاق القائمة */
        #close-sidebar {
            display: none;
        }

        @media (max-width: 768px) {
            #close-sidebar {
                display: block;
            }
        }

        /* تحسينات إضافية للتصميم العربي */
        .table-header {
            text-align: right;
        }

        .table-cell {
            text-align: right;
        }

        /* تحسينات للأزرار */
        .header-buttons {
            display: flex;
            gap: 1rem;
        }

        .header-buttons button {
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .header-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* تحسينات خاصة لشريط البحث */
        .search-section {
            display: flex;
            align-items: center;
            padding: 1rem 0;
        }

        .search-input-container {
            position: relative;
        }

        .search-input-container input {
            width: 100%;
            padding-right: 2.5rem;
        }

        .search-input-container i {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .filter-container select {
            width: 100%;
        }

        .user-count {
            font-weight: bold;
        }

        /* تحسينات للجدول على الشاشات الصغيرة */
        @media (max-width: 640px) {
            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 600px;
            }

            .table-actions {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .table-actions button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 flex h-screen">
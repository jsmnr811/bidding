<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f2f2f2;
            height: 100vh;
            margin: 0;
        }

        .id-card {
            width: 350px;
            height: 566px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .header-logos {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .header-logos img {
            height: 40px;
        }

        .event-title {
            font-size: 13px;
            margin-bottom: 15px;
        }

        .profile-pic {
            width: 150px;
            height: 180px;
            object-fit: cover;
            border: 1px solid #ccc;
            margin: 0 auto 10px auto;
            display: block;
        }

        .user-info {
            margin-top: 10px;
        }

        .user-info .name {
            font-size: 16px;
            font-weight: bold;
        }

        .user-info .role {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }

        .user-info .department {
            font-size: 12px;
            margin-top: 2px;
        }

        .footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer .category {
            font-size: 12px;
            text-align: left;
        }

        .footer .assignment {
            font-size: 12px;
            text-align: right;
        }

        .qr-code {
            margin: 10px auto 0 auto;
            width: 80px;
            height: 80px;
        }

        .powered {
            position: absolute;
            bottom: 8px;
            left: 0;
            right: 0;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <!-- Logos -->
        <div class="header-logos">
            <img src="logo1.png" alt="Logo1">
            <img src="logo2.png" alt="Logo2">
            <img src="logo3.png" alt="Logo3">
        </div>

        <!-- Event title -->
        <div class="event-title">
            Launching of the New World Bank-funded Projects of the Department of Agriculture
        </div>

        <!-- Profile Picture -->
        <img class="profile-pic" src="user-photo.jpg" alt="Profile Picture">

        <!-- User Information -->
        <div class="user-info">
            <div class="name">{{ $user->name }}</div>
            <div class="role">{{ $user->position }}</div>
            <div class="department">{{ $user->department }}</div>
        </div>

        <!-- QR Code -->
        <img class="qr-code" src="qr-code.png" alt="QR Code">

        <!-- Footer -->
        <div class="footer">
            <div class="category">
                Category:<br>
                DA / PRDP<br>
                Employees
            </div>
            <div class="assignment">
                Assignment:<br>
                Table: {{ $user->table }}<br>
                Seat: {{ $user->seat }}
            </div>
        </div>

        <div class="powered">Powered by: DA-ICTS</div>
    </div>
</body>
</html>

<?php include '../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/Images/fav.png">
    <title>User Login</title>
</head>
<body style="margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #e0e7ff, #f4f4f9); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden;">
    <div style="display: flex; flex-direction: row; min-height: calc(100vh - 60px); width: 100%; position: relative;">
        <!-- Image Side -->
        <div style="flex: 1; background: url('../assets/Images/login-bg.jpg') no-repeat center/cover; display: none; position: relative; min-height: 100%;">
            <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.3);"></div>
        </div>
        <!-- Form Side -->
        <div style="flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; background: transparent;">
            <div style="max-width: 400px; width: 100%; padding: 30px; background: #ffffff; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); transition: transform 0.3s ease;">
                <h2 style="font-size: 2rem; color: #1a1a1a; text-align: center; margin-bottom: 25px; font-weight: 600;">Welcome Back</h2>
                <form method="POST" action="../actions/user_login.php" style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="position: relative;">
                        <input type="email" name="email" placeholder="Email" required style="width: 100%; padding: 14px 45px 14px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; color: #333; background: #f9fafb; transition: border-color 0.3s, box-shadow 0.3s;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1);'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';">
                        <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 1.2rem;">✉</span>
                    </div>
                    <div style="position: relative;">
                        <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 14px 45px 14px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; color: #333; background: #f9fafb; transition: border-color 0.3s, box-shadow 0.3s;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1);'" onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';">
                        <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 1.2rem;">🔒</span>
                    </div>
                    <button type="submit" style="background: #3b82f6; color: #ffffff; padding: 14px; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 500; cursor: pointer; transition: background 0.3s ease, transform 0.2s ease;" onmouseover="this.style.background='#2563eb'; this.style.transform='scale(1.02);'" onmouseout="this.style.background='#3b82f6'; this.style.transform='scale(1);'">Login</button>
                </form>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                    <a href="../auth/register.php" style="flex: 1; text-align: center; color: #3b82f6; text-decoration: none; font-size: 0.95rem; padding: 10px; border: 1px solid #3b82f6; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='#3b82f6'; this.style.color='#ffffff';" onmouseout="this.style.background='transparent'; this.style.color='#3b82f6';">Register</a>
                    <a href="../user/forgot_password.php" style="flex: 1; text-align: center; color: #3b82f6; text-decoration: none; font-size: 0.95rem; padding: 10px; border: 1px solid #3b82f6; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.background='#3b82f6'; this.style.color='#ffffff';" onmouseout="this.style.background='transparent'; this.style.color='#3b82f6';">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            body > div {
                flex-direction: column;
            }
            div[style*="background: url"] {
                display: block;
                height: 30vh;
            }
            div[style*="justify-content: center"] {
                padding: 15px;
                min-height: 70vh;
            }
            div[style*="max-width: 400px"] {
                padding: 20px;
                box-shadow: none;
                background: #ffffff;
            }
            h2[style*="font-size: 2rem"] {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 480px) {
            div[style*="max-width: 400px"] {
                padding: 15px;
            }
            h2[style*="font-size: 2rem"] {
                font-size: 1.5rem;
            }
            button[style*="font-size: 1.1rem"] {
                font-size: 1rem;
                padding: 12px;
            }
            a[style*="font-size: 0.95rem"] {
                font-size: 0.85rem;
                padding: 8px;
            }
        }

        @media (min-width: 769px) {
            div[style*="background: url"] {
                display: block;
            }
        }
    </style>
</body>
</html>
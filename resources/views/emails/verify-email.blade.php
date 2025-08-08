<!-- resources/views/emails/verify-email.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            width: 100%;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .hero {
            border: 1px solid #000;
            background-color: #fff;
            padding: 20px;
            max-width: 600px;
            width: 90%;
            margin: 20px auto;
        }

        .email-img img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .contents h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .contents h4 {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn-link a {
            text-decoration: none;
        }

        .btn-link button {
            background-color: #317d7d;
            color: #fff;
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        .social-icons a {
            margin: 0 10px;
            text-decoration: none;
        }

        .social-icons img {
            width: 16px;
            height: 16px;
            vertical-align: middle;
        }

        @media only screen and (max-width: 600px) {
            .hero {
                padding: 15px;
                margin: 10px auto;
            }

            .contents h2 {
                font-size: 20px;
            }

            .contents h4 {
                font-size: 14px;
            }

            .btn-link button {
                padding: 8px 16px;
                font-size: 14px;
                cursor: pointer;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
            <tr>
                <td>
                    <div class="hero">
                        <div style="text-align: center;">
                            <img src="{{ $data['imageUrl'] }}" alt="Mail Image" style="max-width: 50%; height: auto;">
                        </div>
                        <div class="contents">
                            <h2>Verify Your Email to Finish Signing Up!</h2>
                            <h4>
                                Thank you {{ ucfirst($data['name']) }} for choosing us!<br>
                                Please confirm that {{ $data['email'] }} is your email address<br>
                                by clicking the button below or use this link:<br>
                                <p style="font-size: smaller">
                                    <a href="{{ $data['verificationUrl'] }}"
                                        style="color: #2323b6;">{{ $data['verificationUrl'] }}</a>
                                </p>
                                within 24 hours.
                            </h4>
                        </div>
                        <div class="btn-link">
                            <a href="{{ $data['verificationUrl'] }}">
                                <button>Verify Email</button>
                            </a>
                        </div>
                        <hr style="border: 1px solid #ddd;">
                        <div class="footer">
                            <p>© {{ date('Y') }} ETS. All rights reserved.</p>
                            <div class="social-icons">
                                <a href="https://twitter.com" target="_blank">Twitter</a> |
                                <a href="https://facebook.com" target="_blank">Facebook</a> |
                                <a href="https://plus.google.com" target="_blank">Google+</a>
                            </div>
                            <p>Need help? <a href="mailto:mhrznaa.980@gmail.com" style="color: #0000EE;">Contact
                                    Support</a></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
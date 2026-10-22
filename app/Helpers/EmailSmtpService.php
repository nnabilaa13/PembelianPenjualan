<?php

namespace App\Helpers;

use App\Models\Users;
use Carbon\Carbon;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSmtpService
{
    protected $smtpDebug;

    protected $host;

    protected $smtpAuth;

    protected $senderEmail;

    protected $password;

    protected $smtpSecure;

    protected $port;

    protected $emailName;

    public function __construct()
    {
        // $this->smtpDebug = 0;
        // $this->host = 'smtp.gmail.com';
        // $this->smtpAuth = true;
        // $this->senderEmail = 'mandep.official@gmail.com';
        // $this->password = 'ithsftngpivzlymo';
        // $this->smtpSecure = 'tls';
        // $this->port = 587;
        // $this->emailName = 'Mandep Official';

        $this->smtpDebug = 0;
        $this->host = 'smtp.mailgun.org';
        $this->smtpAuth = true;
        $this->senderEmail = 'halobos@mandep.id';
        $this->password = 'cc3daa06e5c01ff0bd9ed88ef38ead69-653fadca-b338b933';
        $this->smtpSecure = 'tls';
        $this->port = 587;
        $this->emailName = 'K3 Apps Official';
    }

    // ========== [ Compose Email ] ================
    public function composeEmail($params)
    {
        $mail = new PHPMailer(true); // Passing `true` enables exceptions

        try {
            // Email server settings
            $mail->SMTPDebug = $this->smtpDebug;
            $mail->isSMTP();
            $mail->Host = $this->host; //  smtp host
            $mail->SMTPAuth = $this->smtpAuth;
            $mail->Username = $this->senderEmail; //  sender username
            $mail->Password = $this->password; // sender password
            $mail->SMTPSecure = $this->smtpSecure; // encryption - ssl/tls
            $mail->Port = $this->port; // port - 587/465
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom($this->senderEmail, $this->emailName);
            $mail->addAddress($params['email']);

            // $mail->addReplyTo('sender@example.com', 'SenderReplyName');

            $mail->isHTML(true);                // Set email content format to HTML

            if ($params['type'] == 'forgot_password') {
                $mail->Subject = 'Reset Password Akun ABC ERP';
                $mail->Body = view('emails.forgot_password', [
                    'email' => $params['email'],
                    'token' => $params['code'],
                ])->render();
            } elseif ($params['type'] == 'registrations') {
                $mail->Subject = 'Selamat Datang di Aplikasi ABC ERP';
                $mail->Body = view('emails.registration', [
                    'name' => $params['name'],
                    'email' => $params['email'],
                    'password' => $params['password'],
                ])->render();
            } elseif ($params['type'] == 'new_passwords') {
                $userAgent = request()->header('User-Agent');
                $browser = 'Unknown Browser';
                if (strpos($userAgent, 'Chrome') !== false) {
                    $browser = 'Chrome';
                } elseif (strpos($userAgent, 'Firefox') !== false) {
                    $browser = 'Firefox';
                } elseif (strpos($userAgent, 'Safari') !== false) {
                    $browser = 'Safari';
                } elseif (strpos($userAgent, 'Edge') !== false) {
                    $browser = 'Edge';
                }

                $platform = 'Unknown OS';
                if (strpos($userAgent, 'Windows') !== false) {
                    $platform = 'Windows';
                } elseif (strpos($userAgent, 'Mac OS') !== false) {
                    $platform = 'macOS';
                } elseif (strpos($userAgent, 'Android') !== false) {
                    $platform = 'Android';
                } elseif (strpos($userAgent, 'iPhone') !== false) {
                    $platform = 'iOS';
                }

                $ipRaw = request()->ip() ?? '8.8.8.8';

                $location = 'Unknown Location';

                $response = @file_get_contents("http://ip-api.com/json/{$ipRaw}");

                if ($response !== false) {
                    $data = json_decode($response, true);

                    if (isset($data['status']) && $data['status'] === 'success') {
                        $city = $data['city'] ?? '';
                        $country = $data['countryCode'] ?? '';
                        $location = trim($city.', '.$country);
                    }
                }

                $deviceInfo = $browser.' on '.$platform;
                $time = Carbon::now()->locale('id')->translatedFormat('d F Y, H:i').' WIB';
                $ipRaw = request()->ip() ?? '0.0.0.0';
                $parts = explode('.', $ipRaw);
                $ip = $parts[0].'.'.$parts[1].'.··.··';
                $mail->Subject = 'Password Baru Aplikasi ABC ERP Anda';
                $mail->Body = view('emails.new_passwords', [
                    'name' => $params['name'],
                    'email' => $params['email'],
                    'password' => $params['password'],
                    'time' => $time,
                    'deviceInfo' => $deviceInfo,
                    'location' => $location,
                    'ip' => $ip,
                ])->render();
            } elseif ($params['type'] == 'add_user') {
                $mail->Subject = 'Anda Diundang Untuk Masuk ke Aplikasi K3';
                // $mail->Body    = '
                //     <html>
                //         <body>
                //             <p>Yth,</p>
                //             <p>'.$params['name'].'</p>
                //             <br>
                //             <p>Anda diundang oleh '.$params['company_name'].' untuk berkolaborasi di MANDEP</p>
                //             <p>Berikut adalah akun detail anda :</p>
                //             <a href="https://app.mandep.id">https://app.mandep.id</a>
                //             <p>Username : '.$params['email'].'</p>
                //             <p>Password : '.$params['password'].'</p>
                //             <p>HARAP SEGERA GANTI PASSWORD ANDA, SETELAH ANDA LOGIN PERTAMA KALI</p>
                //             <p>Terimakasih</p>
                //         </body>
                //     </html>
                // ';

                $mail->Body = view('emails.add_user', [
                    'name' => $params['name'],
                    'role_name' => $params['role_name'],
                    'email' => $params['email'],
                    'password' => $params['password'],
                ])->render();
            } elseif ($params['type'] == 'add_user_existing') {

                $total_user = Users::count();
                $mail->Subject = 'Anda Diundang Untuk Masuk ke ABC ERP';
                // $mail->Body    = '
                //     <html>
                //         <body>
                //             <p>Yth,</p>
                //             <p>'.$params['name'].'</p>
                //             <br>
                //             <p>Anda diundang oleh '.$params['company_name'].' untuk berkolaborasi di MANDEP</p>
                //             <p>Akun email anda terdeteksi telah terdaftar pada sistem MANDEP</p>
                //             <p>Silahkan login menggunakan Email dan Password yang telah anda buat sewaktu mendaftar.</p>
                //             <a href="https://app.mandep.id">https://app.mandep.id</a>
                //             <p>Jika lupa password, anda dapat melakukan pemulihan akun dengan cara klik <a href="https://app.mandep.id/forgot-password">DISINI</a></p>
                //             <p>Terimakasih</p>
                //         </body>
                //     </html>
                // ';
                $mail->Body = view('emails.add_user_existing', [
                    'name' => $params['name'],
                    'role_name' => $params['role_name'],
                    'email' => $params['email'],
                    'company_name' => $params['company_name'],
                    'total_user' => $total_user,
                ])->render();
            }

            if (! $mail->send()) {
                return [
                    'status' => 'error',
                    'message' => 'Email gagal terkirim.',
                    'data' => $mail->ErrorInfo,
                ];
            } else {
                return [
                    'status' => 'success',
                    'message' => 'Harap cek Kotak Masuk/Spam pada email anda.',
                    'data' => null,
                ];
            }

        } catch (Exception $e) {
            \Log::info('============== EMAIL FAILURE');
            \Log::info($e);
            \Log::info('============== EMAIL FAILURE');

            return [
                'status' => 'error',
                'message' => 'Email gagal terkirim.',
                'data' => null,
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Harap cek email anda.',
            'data' => null,
        ];
    }
}

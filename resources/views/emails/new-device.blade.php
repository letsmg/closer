<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Novo Login Detectado - Closer</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; margin: 20px 0; }
        .alert { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; }
        .button { display: inline-block; background: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; }
        .footer { text-align: center; color: #6b7280; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Alerta de Segurança</h1>
        </div>
        
        <div class="content">
            <p>Olá {{ $user->name }},</p>
            
            <p>Detectamos um login na sua conta de um <strong>novo dispositivo</strong>:</p>
            
            <div class="alert">
                <p><strong>📱 Dispositivo:</strong> {{ $deviceInfo['platform'] ?? 'Desconhecido' }}</p>
                <p><strong>🌐 IP:</strong> {{ $deviceInfo['ip'] ?? 'Desconhecido' }}</p>
                <p><strong>📍 Localização:</strong> {{ $deviceInfo['location'] ?? 'Desconhecida' }}</p>
                <p><strong>⏰ Data/Hora:</strong> {{ $time }}</p>
            </div>
            
            <p>Se foi você, pode ignorar este email. Nenhuma ação é necessária.</p>
            
            <p><strong>Não reconhece este dispositivo?</strong></p>
            <p>Se você não fez este login, sua conta pode estar comprometida. Recomendamos:</p>
            <ol>
                <li>Alterar sua senha imediatamente</li>
                <li>Ativar autenticação de dois fatores (2FA)</li>
                <li>Revisar dispositivos autorizados</li>
            </ol>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/2fa') }}" class="button">Proteger Minha Conta</a>
            </p>
        </div>
        
        <div class="footer">
            <p>Este é um email automático de segurança do Closer.</p>
            <p>© {{ date('Y') }} Closer. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>

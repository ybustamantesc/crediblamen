<style>
    .servicont-login-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #073048 0%, #0d4563 50%, #1a5f7d 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .servicont-login-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: moveBackground 30s linear infinite;
    }
    
    @keyframes moveBackground {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }
    
    .servicont-login-card {
        background: rgba(255, 255, 255, 0.99);
        border-radius: 16px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
        overflow: hidden;
        max-width: 480px;
        width: 100%;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .servicont-header {
        background: linear-gradient(135deg, #073048 0%, #0a5a73 100%);
        padding: 40px 30px;
        text-align: center;
        position: relative;
    }
    
    .servicont-header::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.5), rgba(255,255,255,0.3));
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .servicont-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 20px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255, 255, 255, 0.25);
    }
    
    .servicont-icon i {
        font-size: 40px;
        color: #ffffff;
    }
    
    .servicont-title {
        color: #ffffff;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        letter-spacing: 1px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.15);
    }
    
    .servicont-subtitle {
        color: rgba(255, 255, 255, 0.95);
        font-size: 14px;
        margin: 0;
        font-weight: 400;
        letter-spacing: 0.5px;
    }
    
    .servicont-body {
        padding: 40px 35px;
    }
    
    .servicont-form-group {
        margin-bottom: 25px;
    }
    
    .servicont-label {
        display: block;
        color: #073048;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 9px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    
    .servicont-input-wrapper {
        position: relative;
    }
    
    .servicont-input {
        width: 100%;
        padding: 14px 20px 14px 50px;
        border: 2px solid #e0e8f0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #f9fbfd;
        color: #073048;
    }
    
    .servicont-input::placeholder {
        color: #a8b5c4;
    }
    
    .servicont-input:focus {
        outline: none;
        border-color: #073048;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(7, 48, 72, 0.08);
    }
    
    .servicont-input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #8b97a8;
        font-size: 18px;
    }
    
    .servicont-remember-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .servicont-checkbox {
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .servicont-checkbox input {
        margin-right: 8px;
        cursor: pointer;
        accent-color: #073048;
    }
    
    .servicont-checkbox label {
        color: #5a6c7d;
        font-size: 14px;
        cursor: pointer;
        margin: 0;
    }
    
    .servicont-forgot-link {
        color: #073048;
        font-size: 14px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .servicont-forgot-link:hover {
        color: #0a5a73;
        text-decoration: underline;
    }
    
    .servicont-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #073048 0%, #0a5a73 100%);
        border: none;
        border-radius: 10px;
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(7, 48, 72, 0.28);
        position: relative;
        overflow: hidden;
    }
    
    .servicont-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    
    .servicont-btn:hover::before {
        left: 100%;
    }
    
    .servicont-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(7, 48, 72, 0.38);
        background: linear-gradient(135deg, #0a5a73 0%, #073048 100%);
    }
    
    .servicont-btn:active {
        transform: translateY(0);
    }
    
    .servicont-footer {
        padding: 20px;
        text-align: center;
        background: #f5f7fb;
        border-top: 1px solid #e8ecf2;
    }
    
    .servicont-footer-text {
        color: #5a6c7d;
        font-size: 13px;
        margin: 0;
    }
</style>

<div class="servicont-login-wrapper">
    <div class="servicont-login-card">
        <div class="servicont-header">
            <div class="servicont-icon" style="background:transparent;border:none;">
                <img src="<?php echo base_url('public/img/sistema/8150973426.jpg'); ?>" alt="REDI GLAMEN" style="max-width:150px;max-height:110px;object-fit:contain;">
            </div>
            <h1 class="servicont-title">SERVICREDIT</h1>
            <p class="servicont-subtitle">Sistema de Control para Financieras</p>
        </div>
        
        <div class="servicont-body">
            <?php if ($message = $this->session->flashdata('error')) : ?>
                <script>
                    Swal.fire({
                        title: 'Mensaje',
                        text: '<?php echo $message; ?>',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                </script>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo base_url('login/auth') ?>">
                <div class="servicont-form-group">
                    <label class="servicont-label">Correo electrónico</label>
                    <div class="servicont-input-wrapper">
                        <i class="ik ik-user servicont-input-icon"></i>
                        <input type="email" name="email" class="servicont-input" placeholder="usuario@empresa.com" value="<?php echo set_value('email'); ?>" required>
                    </div>
                </div>

                <div class="servicont-form-group">
                    <label class="servicont-label">Contraseña</label>
                    <div class="servicont-input-wrapper">
                        <i class="ik ik-lock servicont-input-icon"></i>
                        <input type="password" name="password" class="servicont-input" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="servicont-remember-row">
                    <div class="servicont-checkbox">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <label for="remember">Recordarme</label>
                    </div>
                    <a href="<?php echo base_url('login/forgot'); ?>" class="servicont-forgot-link">¿Olvidó su contraseña?</a>
                </div>

                <button type="submit" class="servicont-btn">
                    <i class="ik ik-arrow-right-circle" style="margin-right: 8px;"></i>
                    INICIAR SESIÓN
                </button>
            </form>
        </div>
        
        <div class="servicont-footer">
            <p class="servicont-footer-text">&copy; <?php echo date('Y'); ?> SERVICREDIT · Todos los derechos reservados</p>
        </div>
    </div>
</div>

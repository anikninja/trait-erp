<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M13 1.96814L1 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
					<path d="M1 1.96814L13 13.9522" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
				</svg>
			</button>
			<div class="modal-body">
				<div class="main-content sign-in-form">
					<h3 class="form-title">Welcome Back</h3>
					<p>Login with your email/phone & password</p>
					<?= form_open( 'login', 'class="login-form"' ); ?>
					<input type="text" name="identity" placeholder="<?= lang( 'identity1' ); ?>" required>
					<input type="password" name="password" placeholder="<?= lang( 'password' ); ?>" required height="48px">
					<input type="hidden" name="remember_me" value="1">
					<button type="submit" class="button"><?= lang( 'continue' ); ?></button>
					<?= form_close(); ?>

					<span class="form-divider">or</span>

                    <a href="<?= site_url( 'social_auth/login/Facebook' ); ?>" class="fb-sign-in">
                        <img src="<?= $assets; ?>images/social-auth-button/fb-button-normal.png" alt="">
                    </a>
					<a href="<?= site_url( 'social_auth/login/Google' ); ?>" class="google-sign-in">
                        <img src="<?= $assets; ?>images/social-auth-button/google-button-normal.png" alt="">
                        <img src="<?= $assets; ?>images/social-auth-button/google-button-pressed.png" alt="">
                    </a>

					<p style="padding: 20px 0px;">Don't have any account? <a href="#" data-modal="sign-up">Sign Up</a></p>
					<div class="forgot-pass">Forgot your password? <a href="#" data-modal="forgot-password">Reset It</a></div>
				</div>
				<div class="main-content sign-up-form" style="display: none;">
					<h3 class="form-title">Sign Up</h3>
					<p>By signing up, you agree to NamiBD</p>
					<?= form_open( 'register', 'class="login-form"' ); ?>
					<input type="text" name="name" placeholder="<?= lang( 'full_name' ); ?>" required>
					<!-- input type="text" name="first_name" placeholder="<?= lang( 'first_name' ); ?>" pattern=".{3,10}" required>
					<input type="text" name="last_name" placeholder="<?= lang( 'last_name' ); ?>" pattern=".{3,10}" required>
					<input type="text" name="company" placeholder="<?= lang( 'company' ); ?>" pattern=".{3,20}" required -->
					<input type="text" name="phone" placeholder="<?= lang( 'phone' ); ?>" pattern="(\d){11,}" required>
					<input type="email" name="email" placeholder="<?= lang( 'email' ); ?>" required>
					<!-- input type="text" name="username" placeholder="<?= lang( 'username' ); ?>" pattern=".{3,10}" required -->
					<input type="password" name="password" placeholder="Create New Password For Your NamiBD Account" pattern=".{6,}" required>
					<input type="password" name="password_confirm" placeholder="Confirm Your New Password" pattern=".{6,}" required>
					<input type="text" name="referral_id" placeholder="Referral ID (Optional)">
					<p style="padding: 20px 0px 30px;">By signing up, you agree to NamiBD's <a href="<?= site_url( 'page/terms-of-service' ); ?>">Terms &amp; Condition</a></p>
					<button type="submit" class="button"><?= lang( 'continue' ); ?></button>
					<?= form_close(); ?>

					<span class="form-divider">or</span>

                    <a href="<?= site_url( 'social_auth/login/Facebook' ); ?>" class="fb-sign-in">
                        <img src="<?= $assets; ?>images/social-auth-button/fb-button-normal.png" alt="">
                    </a>
                    <a href="<?= site_url( 'social_auth/login/Google' ); ?>" class="google-sign-in">
                        <img src="<?= $assets; ?>images/social-auth-button/google-button-normal.png" alt="">
                        <img src="<?= $assets; ?>images/social-auth-button/google-button-pressed.png" alt="">
                    </a>

					<p style="padding: 20px 0px;">Already have an account? <a href="#" data-modal="sign-in">Login</a></p>
				</div>
				<div class="main-content forgot-password-form" style="display: none">
					<h3 class="form-title">Forgot Password</h3>
					<p>We'll send you a link to reset your password</p>
					<?= form_open( 'forgot_password', 'class="login-form"' ); ?>
					<input type="text" name="identity" placeholder="<?= lang( 'identity1' ); ?>" required>
					<button type="submit" class="button">Reset Password</button>
					<?= form_close(); ?>
					<p style="padding: 20px 0px;">Back to <a href="#" data-modal="sign-in">Login</a></p>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Login Modal End -->

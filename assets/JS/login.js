function handleRegister(event) {
	event.preventDefault();
	showAuthMessage("Registration Successful! Redirecting to Login...", 'success');
	setTimeout(() => {
		window.location.href = "login.html";
	}, 1200);
}

async function handleLogin(event) {
	event.preventDefault();
	const email = document.getElementById("email").value.trim();
	const password = document.getElementById("password").value;
	const loginBtn = document.getElementById("login-btn");
	const loginText = document.getElementById("login-text");
	loginBtn.classList.add("loading");
	loginText.textContent = "Logging in...";
	loginBtn.disabled = true;
	const result = await window.authManager.login(email, password);
	if (result.success) {
		loginText.textContent = "Login Successful!";
		showAuthMessage(result.message, 'success');
		setTimeout(() => {
			window.location.href = "main.html";
		}, 1200);
	} else {
		// Show specific error messages for password/email issues
		if (result.message.toLowerCase().includes('password')) {
			showAuthMessage('Incorrect password. Please try again.', 'error');
		} else if (result.message.toLowerCase().includes('email')) {
			showAuthMessage('Invalid email address. Please check and try again.', 'error');
		} else {
			showAuthMessage(result.message, 'error');
		}
		loginBtn.classList.remove("loading");
		loginText.textContent = "Login";
		loginBtn.disabled = false;
		if (result.message.toLowerCase().includes('email')) {
			document.getElementById("email").classList.add("error");
		}
		if (result.message.toLowerCase().includes('password')) {
			document.getElementById("password").classList.add("error");
		}
	}
}

function togglePassword() {
	const passwordInput = document.getElementById("password");
	const eyeIcon = document.getElementById("password-eye");

	if (passwordInput.type === "password") {
		passwordInput.type = "text";
		eyeIcon.className = "fas fa-eye-slash";
	} else {
		passwordInput.type = "password";
		eyeIcon.className = "fas fa-eye";
	}
}

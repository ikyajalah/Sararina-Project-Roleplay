<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SCRP</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h1>STARLIGHT</h1>
        <ul class="menu">
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- Konten utama bisa di sini -->
        <h2>STARLIGHT</h2>
        <p>GALERY</p>
        <div class="slider">
			<img src="assets/img/home2.png" class="slide" style="display: block;">
			<img src="assets/img/home1.jpg" class="slide">
			<button class="prev" onclick="plusSlides(-1)">&#10094;</button>
			<button class="next" onclick="plusSlides(1)">&#10095;</button>
		</div>
    </div>
    <script>
		let slideIndex = 0;
		const slides = document.getElementsByClassName("slide");

		function showSlide(index) {
		  if (index >= slides.length) slideIndex = 0;
		  if (index < 0) slideIndex = slides.length - 1;

		  for (let i = 0; i < slides.length; i++) {
		    slides[i].style.display = "none";
		  }

		  slides[slideIndex].style.display = "block";
		}

		function plusSlides(n) {
		  slideIndex += n;
		  showSlide(slideIndex);
		}

		// Tampilkan slide pertama saat awal
		showSlide(slideIndex);
	</script>
</body>
</html>


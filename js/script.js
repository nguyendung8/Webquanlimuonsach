let userBox = document.querySelector('.header .header-2 .user-box');

document.querySelector('#user-btn').onclick = () =>{
   userBox.classList.toggle('active');
   navbar.classList.remove('active');
}

let navbar = document.querySelector('.header .header-2 .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   userBox.classList.remove('active');
}

window.onscroll = () =>{
   userBox.classList.remove('active');
   navbar.classList.remove('active');

   if(window.scrollY > 60){
      document.querySelector('.header .header-2').classList.add('active');
   }else{
      document.querySelector('.header .header-2').classList.remove('active');
   }
}

// SlideShow
  let currentImageIndex = 0;
  const images = [
    'slide1.jpg',
    'slide2.jpg',
    'slide3.jpg',
    'slide4.jpg',
  ];

  function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
    updateSlideshow();
  }

  function updateSlideshow() {
    const slideshowContainer = document.getElementById('slideshow-container');
    slideshowContainer.innerHTML = `<img src="${images[currentImageIndex]}" alt="Slideshow Image">`;

    setTimeout(nextImage, 3000); // Chuyển đổi ảnh sau 3 giây
  }

  updateSlideshow(); // Bắt đầu slide show

<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- primary meta tags-->
  <title>QuickBite Juba|Order from Mama Tereza's Restaurant</title>
  <meta name="title" content="QuickBite - Where every bite tells a story">
  <meta name="description" content="Welcome Mama Tereza's Restaurant Website">

  <!-- favarite icons-->
  <link rel="shortcut icon" href="./logo1.png" type="logo1">

  <!-- google fonts link-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap" rel="stylesheet">

  <!--CSS link - direct loading for better performance -->
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/notification.css">

  <!-- 
    - preload images
-->
  <link rel="preload" as="image" href="./assets/images/Image1.jpg">
  <head>
    <link rel="preload" as="image" href="./assets/images/hero-slider1.jpg">
    </head>
  <link rel="preload" as="image" href="./assets/images/hero-slider-3.jpg">

</head>

<body id="top">

  <!-- 
    - #PRELOADER
  -->

  <div class="preload" data-preaload>
    <div class="circle"></div>
    <p class="text">QuickBite</p>
  </div>


  <!-- 
    - #TOP BAR
  -->

  <div class="topbar">
    <div class="container">

      <address class="topbar-item">
        <div class="icon">
          <ion-icon name="location-outline" aria-hidden="true"></ion-icon>
        </div>

        <span class="span">
         QuickBite|Mama Tereza's Restaurant,Juba,Sherikaat 8888, SSD
        </span>
      </address>

      <div class="separator"></div>

      <div class="topbar-item item-2">
        <div class="icon">
          <ion-icon name="time-outline" aria-hidden="true"></ion-icon>
        </div>

        <span class="span">Daily : 5.00 am to 12.00 pm</span>
      </div>

      <a href="tel:+211922488868" class="topbar-item link">
        <div class="icon">
          <ion-icon name="call-outline" aria-hidden="true"></ion-icon>
        </div>

        <span class="span">+211 9224 888 68</span>
      </a>

      <div class="separator"></div>

      <a href="mailto:quickbite@gmail.com" class="topbar-item link">
        <div class="icon">
          <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>
        </div>

        <span class="span">quickbite@gmail.com</span>
      </a>

    </div>
  </div>


  <!-- 
    - #HEADER
  -->

  <header class="header" data-header>
    <div class="container">

      <a href="#" class="logo">
        <img src="./assets/images/logo1.png" width="160" height="50" alt="Grilli - Home">
      </a>

      <nav class="navbar" data-navbar>

        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>

        <a href="#" class="logo">
          <img src="./assets/images/logo1.png" width="160" height="50" alt="Grilli - Home">
        </a>

        <ul class="navbar-list">

          <li class="navbar-item">
            <a href="#home" class="navbar-link hover-underline active">
              <div class="separator"></div>

              <span class="span">Home</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#menu" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Menus</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#about" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">About Us</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#chefs" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Our Chefs</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#contact" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Contact</span>
            </a>
          </li>

        </ul>

        <div class="text-center">
          <p class="headline-1 navbar-title">Visit Us</p>

          <address class="body-4">
            QuickBite|Mama Tereza's Restaurant,Juba,Sherikaat 8888, SSD
          </address>

          <p class="body-4 navbar-text">Open: 5.00 am - 12.00pm</p>

          <a href="mailto:quickbite@gmail.com" class="body-4 sidebar-link">quickbite@gmail.com</a>

          <div class="separator"></div>

          <p class="contact-label">Booking Request</p>

          <a href="tel:+211922488868" class="body-1 contact-number hover-underline">
            +211 9224 888 68
          </a>
        </div>

      </nav>

      <div class="header-actions">
        <a href="auth/login_fixed.php" class="btn btn-secondary" id="loginBtn">
          <span class="text text-1">Login</span>
          <span class="text text-2" aria-hidden="true">Login</span>
        </a>
        
        <a href="#" class="btn btn-primary" id="dashboardBtn" style="display: none;">
          <span class="text text-1">Dashboard</span>
          <span class="text text-2" aria-hidden="true">Dashboard</span>
        </a>
      </div>

      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>

      <div class="overlay" data-nav-toggler data-overlay></div>

    </div>
  </header>


  <main>
    <article>

      <!-- 
        - #HERO
      -->

      <section class="hero text-center" aria-label="home" id="home">

        <ul class="hero-slider" data-hero-slider>

          <li class="slider-item active" data-hero-slider-item>

            <div class="slider-bg">
              <img src="./assets/images/hero-slider1.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>

            <p class="label-2 section-subtitle slider-reveal">Trusted Recipes</p>

            <h1 class="display-1 hero-title slider-reveal">
              Where Taste Reigns  <br>
              Supreme
            </h1>

            <p class="body-2 hero-text slider-reveal">
              The Perfect Family Feast
            </p>

            <a href="#menu" class="btn btn-primary slider-reveal">
              <span class="text text-1">View Our Menu</span>

              <span class="text text-2" aria-hidden="true">View Our Menu</span>
            </a>

          </li>

          <li class="slider-item" data-hero-slider-item>

            <div class="slider-bg">
              <img src="./assets/images/hero-slider-2.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>

            <p class="label-2 section-subtitle slider-reveal">Unforgettable Treat</p>

            <h1 class="display-1 hero-title slider-reveal">
              From the Earth, to <br>
              the Table
            </h1>

            <p class="body-2 hero-text slider-reveal">
              The Perfect Family Feast
            </p>

            <a href="#menu" class="btn btn-primary slider-reveal">
              <span class="text text-1">View Our Menu</span>

              <span class="text text-2" aria-hidden="true">View Our Menu</span>
            </a>

          </li>

          <li class="slider-item" data-hero-slider-item>

            <div class="slider-bg">
              <img src="./assets/images/hero-slider-3.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>

            <p class="label-2 section-subtitle slider-reveal">The Ultimate Flavor</p>

            <h1 class="display-1 hero-title slider-reveal">
              Where every Bite <br>
              tells a story
            </h1>

            <p class="body-2 hero-text slider-reveal">
              The Perfect Family Feast
            </p>

            <a href="#menu" class="btn btn-primary slider-reveal">
              <span class="text text-1">View Our Menu</span>

              <span class="text text-2" aria-hidden="true">View Our Menu</span>
            </a>

          </li>

        </ul>

        <button class="slider-btn prev" aria-label="slide to previous" data-prev-btn>
          <ion-icon name="chevron-back"></ion-icon>
        </button>

        <button class="slider-btn next" aria-label="slide to next" data-next-btn>
          <ion-icon name="chevron-forward"></ion-icon>
        </button>

        <a href="#service" class="hero-btn has-after">
          <img src="./assets/images/hero-icon.png" width="48" height="48" alt="booking icon">

          <span class="label-2 text-center span">Book A Table</span>
        </a>

      </section>


      <!-- 
        - #SERVICE
      -->

      <section class="section service bg-black-10 text-center" aria-label="service" id="service">
        <div class="container">

          <p class="section-subtitle label-2">The Crown Jewel of Nile  Taste</p>

          <h2 class="headline-1 section-title">We Offer Unmatched Excellence</h2>

          <p class="section-text">
            We Offer Unmatched Excellence in every dish, defined by Uncompromising Quality, Sensational Flavors, 
            and a passionate commitment to the art of cuisine.
          </p>

          <ul class="grid-list">

            <li>
              <div class="service-card">

                <a href="#" class="has-before hover:shine">
                  <figure class="card-banner img-holder" style="--width: 285; --height: 336;">
                    <img src="./assets/images/service-1.jpg" width="285" height="336" loading="lazy" alt="Breakfast"
                      class="img-cover">
                  </figure>
                </a>

                <div class="card-content">

                  <h3 class="title-4 card-title">
                    <a href="#">Breakfast</a>
                  </h3>

                  <a href="#" class="btn-text hover-underline label-2">View Menu</a>

                </div>

              </div>
            </li>

            <li>
              <div class="service-card">

                <a href="#" class="has-before hover:shine">
                  <figure class="card-banner img-holder" style="--width: 285; --height: 336;">
                    <img src="./assets/images/service-2.jpg" width="285" height="336" loading="lazy" alt="Appetizers"
                      class="img-cover">
                  </figure>
                </a>

                <div class="card-content">

                  <h3 class="title-4 card-title">
                    <a href="#">Signature Dish</a>
                  </h3>

                  <a href="#" class="btn-text hover-underline label-2">View Menu</a>

                </div>

              </div>
            </li>

            <li>
              <div class="service-card">

                <a href="#" class="has-before hover:shine">
                  <figure class="card-banner img-holder" style="--width: 285; --height: 336;">
                    <img src="./assets/images/service-3.jpg" width="285" height="336" loading="lazy" alt="Drinks"
                      class="img-cover">
                  </figure>
                </a>

                <div class="card-content">

                  <h3 class="title-4 card-title">
                    <a href="#">Drinks</a>
                  </h3>

                  <a href="#" class="btn-text hover-underline label-2">View Menu</a>

                </div>

              </div>
            </li>

          </ul>

          <img src="./assets/images/shape-1.png" width="246" height="412" loading="lazy" alt="shape"
            class="shape shape-1 move-anim">
          <img src="./assets/images/shape-2.png" width="343" height="345" loading="lazy" alt="shape"
            class="shape shape-2 move-anim">

        </div>
      </section>


      <!-- 
        - #ABOUT
      -->

      <section class="section about text-center" aria-label="about label" id="about">
        <div class="container">

          <div class="about-content">

            <p class="label-2 section-subtitle" id="about-label">Our Story</p>

            <h2 class="headline-1 section-title">Every Bite Tells a Story</h2>

            <p class="section-text">
              Every Bite Tells a Story, transforming your meal into an unforgettable,
               full sensory journey where the meticulous preparation, the freshest ingredients,
                and the passion for food all converge to write a delicious chapter in your dining memory.
            </p>

            <div class="contact-label">Book Through Call</div>

            <a href="tel:+804001234567" class="body-1 contact-number hover-underline">+211 9224 888 68</a>

            <a href="#" class="btn btn-primary">
              <span class="text text-1">Read More</span>

              <span class="text text-2" aria-hidden="true">Read More</span>
            </a>

          </div>

          <figure class="about-banner">

            <img src="./assets/images/about-banner1.jpg" width="570" height="570" loading="lazy" alt="about banner"
              class="w-100" data-parallax-item data-parallax-speed="1">

            <div class="abs-img abs-img-1 has-before" data-parallax-item data-parallax-speed="1.75">
              <img src="./assets/images/about-abs-image1.jpg" width="285" height="285" loading="lazy" alt=""
                class="w-100">
            </div>

            <div class="abs-img abs-img-2 has-before">
              <img src="./assets/images/badge-3.jpg" width="133" height="134" loading="lazy" alt="">
            </div>

          </figure>

        </div>
      </section>



      <!-- 
        - #MENU
      -->

      <section class="section menu" aria-label="menu-label" id="menu">
        <div class="container">

          <p class="section-subtitle text-center label-2">Special Selection</p>

          <h2 class="headline-1 section-title text-center">Delicious Menu</h2>

          <ul class="grid-list menu-list">

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu-1.png" width="100" height="100" loading="lazy" alt="Greek Salad"
                    class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Kibab Salad</a>
                    </h3>

                    <span class="badge label-1">Seasonal</span>

                    <span class="span title-2">SSP 5500</span>
                  </div>

                  <p class="card-text label-1">
                    Tomatoes, green bell pepper, sliced cucumber onion, olives, and feta cheese.
                  </p>

                  <button class="btn btn-secondary order-btn" onclick="orderItem('Kibab Salad', 5500, './assets/images/menu-1.png')">
                    <span class="text text-1">Order Now</span>
                    <span class="text text-2" aria-hidden="true">Order Now</span>
                  </button>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu-2.png" width="100" height="100" loading="lazy" alt="Lasagne"
                    class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Kwaja</a>
                    </h3>

                    <span class="span title-2">SSP 4500</span>
                  </div>

                  <p class="card-text label-1">
                    Vegetables, cheeses, ground meats, tomato sauce, seasonings and spices
                  </p>

                  <button class="btn btn-secondary order-btn" onclick="orderItem('Kwaja', 4500, './assets/images/menu-2.png')">
                    <span class="text text-1">Order Now</span>
                    <span class="text text-2" aria-hidden="true">Order Now</span>
                  </button>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu-3.png" width="100" height="100" loading="lazy" alt="Butternut Pumpkin"
                    class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Stewed Pumpkin</a>
                    </h3>

                    <span class="span title-2">SSP 5000</span>
                  </div>

                  <p class="card-text label-1">
                    Traditional South Sudanese stewed pumpkin cooked with aromatic spices and herbs.
                  </p>

                  <button class="btn btn-secondary order-btn" onclick="orderItem('Stewed Pumpkin', 5000, './assets/images/menu-3.png')">
                    <span class="text text-1">Order Now</span>
                    <span class="text text-2" aria-hidden="true">Order Now</span>
                  </button>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu-4.png" width="100" height="100" loading="lazy" alt="Tokusen Wagyu"
                    class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Grilled Beef</a>
                    </h3>

                    <span class="badge label-1">New</span>

                    <span class="span title-2">SSP 5000</span>
                  </div>

                  <p class="card-text label-1">
                    Vegetables, cheeses, ground meats, tomato sauce, seasonings and spices.
                  </p>

                  <button class="btn btn-secondary order-btn" onclick="orderItem('Grilled Beef', 5000, './assets/images/menu-4.png')">
                    <span class="text text-1">Order Now</span>
                    <span class="text text-2" aria-hidden="true">Order Now</span>
                  </button>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu-5.png" width="100" height="100" loading="lazy" alt="Olivas Rellenas"
                    class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Stuffed Okra</a>
                    </h3>

                    <span class="span title-2">SSP 5000</span>
                  </div>

                  <p class="card-text label-1">
                    Avocados with crab meat, red onion, crab salad stuffed red bell pepper and green bell pepper.
                  </p>

                  <button class="btn btn-secondary order-btn" onclick="orderItem('Stuffed Okra', 5000, './assets/images/menu-5.png')">
                    <span class="text text-1">Order Now</span>
                    <span class="text text-2" aria-hidden="true">Order Now</span>
                  </button>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu-6.png" width="100" height="100" loading="lazy" alt="Opu Fish"
                    class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Mad Fish</a>
                    </h3>

                    <span class="badge label-1">Seasonal</span>

                    <span class="span title-2">SSP 10000</span>
                  </div>

                  <p class="card-text label-1">
                    Vegetables, cheeses, ground meats, tomato sauce, seasonings and spices
                  </p>

                  <button class="btn btn-secondary order-btn" onclick="orderItem('Mad Fish', 10000, './assets/images/menu-6.png')">
                    <span class="text text-1">Order Now</span>
                    <span class="text text-2" aria-hidden="true">Order Now</span>
                  </button>

                </div>

              </div>
            </li>

          </ul>

          <p class="menu-text text-center">
            During christmas daily from <span class="span">5:00 pm</span> to <span class="span">12:00 pm</span>
          </p>

          <a href="#" class="btn btn-primary">
            <span class="text text-1">View All Menu</span>

            <span class="text text-2" aria-hidden="true">View All Menu</span>
          </a>

          <img src="./assets/images/shape-5.png" width="921" height="1036" loading="lazy" alt="shape"
            class="shape shape-2 move-anim">
          <img src="./assets/images/shape-6.png" width="343" height="345" loading="lazy" alt="shape"
            class="shape shape-3 move-anim">

        </div>
      </section>


      <!-- 
        - #TESTIMONIALS
      -->

      <section class="section testi text-center has-bg-image"
        style="background-image: url('./assets/images/testimonial2.jpg')" aria-label="testimonials">
        <div class="container">

          <div class="quote">"</div>

          <p class="headline-2 testi-text">
            I wanted to thank you for having me over for that wonderful dinner the other evening.
             The meal was truly exceptional.
          </p>

          <div class="wrapper">
            <div class="separator"></div>
            <div class="separator"></div>
            <div class="separator"></div>
          </div>

          <div class="profile">
            <img src="./assets/images/testi-avatar1.jpg" width="100" height="100" loading="lazy" alt="Sam Jhonson"
              class="img">

            <p class="label-2 profile-name">Denis Micheal</p>
          </div>

        </div>
      </section>


      <!-- 
        - #RESERVATION
      -->

      <section class="reservation">
        <div class="container">

          <div class="form reservation-form bg-black-10">

            <form class="form-left" id="reservationForm" onsubmit="return handleReservationSubmit(event)">

              <h2 class="headline-1 text-center">Online Reservation</h2>

              <p class="form-text text-center">
                Booking request <a href="tel:+88123123456" class="link">+211-9224-888-68</a>
                or fill out the order form
              </p>

              <div class="input-wrapper">
                <input type="text" name="name" placeholder="Your Name" autocomplete="off" class="input-field" required>

                <input type="tel" name="phone" placeholder="Phone Number" autocomplete="off" class="input-field" required>
              </div>

              <div class="input-wrapper">

                <div class="icon-wrapper">
                  <ion-icon name="person-outline" aria-hidden="true"></ion-icon>

                  <select name="person" class="input-field" required>
                    <option value="">Select People</option>
                    <option value="1">1 Person</option>
                    <option value="2">2 Person</option>
                    <option value="3">3 Person</option>
                    <option value="4">4 Person</option>
                    <option value="5">5 Person</option>
                    <option value="6">6 Person</option>
                    <option value="7">7 Person</option>
                  </select>

                  <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                </div>

                <div class="icon-wrapper">
                  <ion-icon name="calendar-clear-outline" aria-hidden="true"></ion-icon>

                  <input type="date" name="reservation-date" class="input-field" required>

                  <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                </div>

                <div class="icon-wrapper">
                  <ion-icon name="time-outline" aria-hidden="true"></ion-icon>

                  <select name="time" class="input-field" required>
                    <option value="">Select Time</option>
                    <option value="08:00:00">08 : 00 am</option>
                    <option value="09:00:00">09 : 00 am</option>
                    <option value="10:00:00">10 : 00 am</option>
                    <option value="11:00:00">11 : 00 am</option>
                    <option value="12:00:00">12 : 00 pm</option>
                    <option value="13:00:00">01 : 00 pm</option>
                    <option value="14:00:00">02 : 00 pm</option>
                    <option value="15:00:00">03 : 00 pm</option>
                    <option value="16:00:00">04 : 00 pm</option>
                    <option value="17:00:00">05 : 00 pm</option>
                    <option value="18:00:00">06 : 00 pm</option>
                    <option value="19:00:00">07 : 00 pm</option>
                    <option value="20:00:00">08 : 00 pm</option>
                    <option value="21:00:00">09 : 00 pm</option>
                    <option value="22:00:00">10 : 00 pm</option>
                  </select>

                  <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                </div>

              </div>

              <textarea name="message" placeholder="Message" autocomplete="off" class="input-field"></textarea>

              <button type="submit" class="btn btn-secondary">
                <span class="text text-1">Book A Table</span>

                <span class="text text-2" aria-hidden="true">Book A Table</span>
              </button>

            </form>

            <div class="form-right text-center" style="background-image: url('./assets/images/form-pattern.png')">

              <h2 class="headline-1 text-center">Contact Us</h2>

              <p class="contact-label">Booking Request</p>

              <a href="tel:+88123123456" class="body-1 contact-number hover-underline">+211-9224-888-68</a>

              <div class="separator"></div>

              <p class="contact-label">Location</p>

              <address class="body-4">
                QuickBite|Mama Tereza's Restaurant,Juba,Sherikaat 8888, SSD
              </address>

              <p class="contact-label">Lunch Time</p>

              <p class="body-4">
                Monday to Sunday <br>
                11.00 am - 2.30pm
              </p>

              <p class="contact-label">Dinner Time</p>

              <p class="body-4">
                Monday to Sunday <br>
                05.00 pm - 10.00pm
              </p>

            </div>

          </div>

        </div>
      </section>


      <!-- 
        - #FEATURES
      -->

      <section class="section features text-center" aria-label="features">
        <div class="container">

          <p class="section-subtitle label-2">Why Choose Us</p>

          <h2 class="headline-1 section-title">Our Strength</h2>

          <ul class="grid-list">

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets/images/features-icon-1.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Hygienic Food</h3>

                <p class="label-1 card-text">We maintain the highest standards of food safety and hygiene in our kitchen.</p>

              </div>
            </li>

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets/images/features-icon-2.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Fresh Environment</h3>

                <p class="label-1 card-text">Enjoy your meal in our clean, comfortable, and welcoming dining atmosphere.</p>

              </div>
            </li>

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets/images/features-icon-3.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Skilled Chefs</h3>

                <p class="label-1 card-text">Our experienced chefs bring authentic South Sudanese flavors to every dish.</p>

              </div>
            </li>

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets/images/features-icon-4.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Event and Party</h3>

                <p class="label-1 card-text">Event and party services create memorable moments with great food and perfect setup.</p>

              </div>
            </li>

          </ul>

          <img src="./assets/images/shape-7.png" width="208" height="178" loading="lazy" alt="shape"
            class="shape shape-1">

          <img src="./assets/images/shape-8.png" width="120" height="115" loading="lazy" alt="shape"
            class="shape shape-2">

        </div>
      </section>

      <!-- 
        - #CONTACT
      -->

      <section class="section contact" aria-label="contact" id="contact">
        <div class="container">

          <div class="contact-content">
            <p class="section-subtitle label-2">Get In Touch</p>

            <h2 class="headline-1 section-title">Contact Us</h2>

            <p class="section-text">
              Have questions or special requests? We'd love to hear from you. Reach out to us through any of the channels below.
            </p>

            <div class="contact-info">
              <div class="contact-item">
                <div class="icon-wrapper">
                  <ion-icon name="location-outline"></ion-icon>
                </div>
                <div>
                  <h3 class="title-3">Visit Us</h3>
                  <address class="body-4">
                    QuickBite | Mama Tereza's Restaurant<br>
                    Juba, Sherikaat 8888<br>
                    South Sudan
                  </address>
                </div>
              </div>

              <div class="contact-item">
                <div class="icon-wrapper">
                  <ion-icon name="call-outline"></ion-icon>
                </div>
                <div>
                  <h3 class="title-3">Call Us</h3>
                  <a href="tel:+211922488868" class="body-4 contact-link">+211 9224 888 68</a>
                  <p class="body-4">Mon - Sun: 5:00 AM - 12:00 PM</p>
                </div>
              </div>

              <div class="contact-item">
                <div class="icon-wrapper">
                  <ion-icon name="mail-outline"></ion-icon>
                </div>
                <div>
                  <h3 class="title-3">Email Us</h3>
                  <a href="mailto:quickbite@gmail.com" class="body-4 contact-link">quickbite@gmail.com</a>
                </div>
              </div>

              <div class="contact-item">
                <div class="icon-wrapper">
                  <ion-icon name="time-outline"></ion-icon>
                </div>
                <div>
                  <h3 class="title-3">Opening Hours</h3>
                  <p class="body-4">
                    <strong>Daily:</strong> 5:00 AM - 12:00 PM
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="contact-form-wrapper">
            <form class="contact-form" id="contactForm" onsubmit="return handleContactSubmit(event)">
              <h3 class="headline-2 text-center">Send Us a Message</h3>

              <div class="input-wrapper">
                <input type="text" name="contact_name" placeholder="Your Name" class="input-field" required>
                <input type="email" name="contact_email" placeholder="Your Email" class="input-field" required>
              </div>

              <div class="input-wrapper">
                <input type="tel" name="contact_phone" placeholder="Phone Number" class="input-field" required>
                <input type="text" name="contact_subject" placeholder="Subject" class="input-field" required>
              </div>

              <textarea name="contact_message" placeholder="Your Message" class="input-field" rows="6" required></textarea>

              <button type="submit" class="btn btn-secondary">
                <span class="text text-1">Send Message</span>
                <span class="text text-2" aria-hidden="true">Send Message</span>
              </button>
            </form>
          </div>

        </div>
      </section>


      <!-- 
        - #EVENT
      -->

      <section class="section event bg-black-10" aria-label="event">
        <div class="container">

          <p class="section-subtitle label-2 text-center">Latest Updates</p>

          <h2 class="section-title headline-1 text-center">Upcoming Event</h2>

          <ul class="grid-list">

            <li>
              <div class="event-card has-before hover:shine">

                <div class="card-banner img-holder" style="--width: 350; --height: 450;">
                  <img src="./assets/images/event-1.jpg" width="350" height="450" loading="lazy"
                    alt="Flavour so good you'll try to eat with your eyes." class="img-cover">

                  <time class="publish-date label-2" datetime="2025-12-12">12/12/2025</time>
                </div>

                <div class="card-content">
                  <p class="card-subtitle label-2 text-center">Food, Flavour</p>

                  <h3 class="card-title title-2 text-center">
                    Taste so rich you'll crave it before the first bite.
                  </h3>
                </div>

              </div>
            </li>

            <li>
              <div class="event-card has-before hover:shine">

                <div class="card-banner img-holder" style="--width: 350; --height: 450;">
                  <img src="./assets/images/service4.jpg" width="350" height="450" loading="lazy"
                    alt="Flavour so good you'll try to eat with your eyes." class="img-cover">

                  <time class="publish-date label-2" datetime="2025-12-12">12/12/2025</time>
                </div>

                <div class="card-content">
                  <p class="card-subtitle label-2 text-center">Healthy Food</p>

                  <h3 class="card-title title-2 text-center">
                    Delicious enough to tempt your eyes and your appetite.
                  </h3>
                </div>

              </div>
            </li>

            <li>
              <div class="event-card has-before hover:shine">

                <div class="card-banner img-holder" style="--width: 350; --height: 450;">
                  <img src="./assets/images/event-3.jpg" width="350" height="450" loading="lazy"
                    alt="Flavour so good you'll try to eat with your eyes." class="img-cover">

                  <time class="publish-date label-2" datetime="2025-12-12">12/12/2025</time>
                </div>

                <div class="card-content">
                  <p class="card-subtitle label-2 text-center">Recipe</p>

                  <h3 class="card-title title-2 text-center">
                    Flavour so good you'll try to eat with your eyes.
                  </h3>
                </div>

              </div>
            </li>

          </ul>

          <a href="#" class="btn btn-primary">
            <span class="text text-1">View Our Blog</span>

            <span class="text text-2" aria-hidden="true">View Our Blog</span>
          </a>

        </div>
      </section>

      <!-- 
        - #CHEFS
      -->

      <section class="section chefs bg-black-10" aria-label="chefs" id="chefs">
        <div class="container">

          <p class="section-subtitle label-2 text-center">Meet Our Team</p>

          <h2 class="section-title headline-1 text-center">Our Master Chefs</h2>

          <p class="section-text text-center">
            Our talented chefs bring years of culinary expertise and passion to every dish they create.
          </p>

          <ul class="grid-list">

            <li>
              <div class="chef-card">
                <div class="card-banner img-holder" style="--width: 350; --height: 450;">
                  <img src="./assets/images/chef1.jpg" width="350" height="450" loading="lazy" alt="Chef John Doe" class="img-cover">
                </div>

                <div class="card-content">
                  <h3 class="title-2 card-title">Chef John Doe</h3>
                  <p class="label-1 card-subtitle">Head Chef</p>
                  <p class="card-text label-1">
                    With 15 years of experience in fine dining, Chef John specializes in fusion cuisine.
                  </p>
                </div>
              </div>
            </li>

            <li>
              <div class="chef-card">
                <div class="card-banner img-holder" style="--width: 350; --height: 450;">
                  <img src="./assets/images/chef2.jpg" width="350" height="450" loading="lazy" alt="Chef Maria Santos" class="img-cover">
                </div>

                <div class="card-content">
                  <h3 class="title-2 card-title">Chef Marcus Santos</h3>
                  <p class="label-1 card-subtitle">Pastry Chef</p>
                  <p class="card-text label-1">
                    Award-winning pastry chef known for innovative desserts and artistic presentations.
                  </p>
                </div>
              </div>
            </li>

            <li>
              <div class="chef-card">
                <div class="card-banner img-holder" style="--width: 350; --height: 450;">
                  <img src="./assets/images/chef3.jpg" width="350" height="450" loading="lazy" alt="Chef Ahmed Hassan" class="img-cover">
                </div>

                <div class="card-content">
                  <h3 class="title-2 card-title">Chef Ahmed Hassan</h3>
                  <p class="label-1 card-subtitle">Sous Chef</p>
                  <p class="card-text label-1">
                    Expert in traditional and modern cooking techniques with a passion for local ingredients.
                  </p>
                </div>
              </div>
            </li>

          </ul>

        </div>
      </section>

    </article>
  </main>


  <!-- 
    - #FOOTER
  -->

  <footer class="footer section has-bg-image text-center"
    style="background-image: url('./assets/images/footer-bg.jpg')">
    <div class="container">

      <div class="footer-top grid-list">

        <div class="footer-brand has-before has-after">

          <a href="#" class="logo">
            <img src="./assets/images/logo1.png" width="160" height="50" alt="grilli home">
          </a>

          <address class="body-4">
            QuickBite|Mama Tereza's Restaurant,Juba,Sherikaat 8888, SSD
          </address>

          <a href="mailto:quickbite@gmail.com" class="body-4 contact-link">quickbite@gmail.com</a>

          <a href="tel:+211922488868" class="body-4 contact-link">Booking Request : +211-9224-888-68</a>

          <p class="body-4">
            Open : 09:00 am - 01:00 pm
          </p>

          <div class="wrapper">
            <div class="separator"></div>
            <div class="separator"></div>
            <div class="separator"></div>
          </div>

          <p class="title-1">Get News & Offers</p>

          <p class="label-1">
            Subscribe us & Get <span class="span">25% Off.</span>
          </p>

          <form class="input-wrapper" id="subscribeForm" onsubmit="return handleSubscribeSubmit(event)">
            <div class="icon-wrapper">
              <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>

              <input type="email" name="email_address" placeholder="Your email" autocomplete="off" class="input-field" required>
            </div>

            <button type="submit" class="btn btn-secondary">
              <span class="text text-1">Subscribe</span>

              <span class="text text-2" aria-hidden="true">Subscribe</span>
            </button>
          </form>

        </div>

        <ul class="footer-list">

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Home</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Menus</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">About Us</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Our Chefs</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Contact</a>
          </li>

        </ul>

        <ul class="footer-list">

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Facebook</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Instagram</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Twitter</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Youtube</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Google Map</a>
          </li>

        </ul>

      </div>

      <div class="footer-bottom">

        <p class="copyright">
          &copy; 2025 QuickBite. All Rights Reserved | Crafted by <a href="manyokgchop11@gmail.com"
            target="_blank" class="link">codewithmanyokgchop11@gmail.com</a>
        </p>

      </div>

    </div>
  </footer>


  <!-- 
    - #BACK TO TOP
  -->

  <a href="#top" class="back-top-btn active" aria-label="back to top" data-back-top-btn>
    <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
  </a>


<!-- Javascript link -->
  <script src="./assets/js/script.js"></script>
  <script src="./assets/js/cart-optimized.js"></script>


  <!--ionicon link for simplified loading -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <!-- Inline Form Handlers -->
  <script>
    // Reservation Form Handler
    async function handleReservationSubmit(event) {
      event.preventDefault();
      console.log('Reservation form submitted via inline handler');
      
      const form = event.target;
      const formData = new FormData(form);
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="text text-1">Submitting...</span>';
      
      try {
        const response = await fetch('api/book_table.php', {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          showInlineNotification(data.message, 'success');
          form.reset();
        } else {
          showInlineNotification(data.message || 'Failed to submit reservation', 'error');
        }
      } catch (error) {
        showInlineNotification('Network error. Please check your connection and try again.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      
      return false;
    }
    
    // Contact Form Handler
    async function handleContactSubmit(event) {
      event.preventDefault();
      console.log('Contact form submitted via inline handler');
      
      const form = event.target;
      const formData = new FormData(form);
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="text text-1">Sending...</span>';
      
      try {
        const response = await fetch('api/contact.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            name: formData.get('contact_name'),
            email: formData.get('contact_email'),
            phone: formData.get('contact_phone'),
            subject: formData.get('contact_subject'),
            message: formData.get('contact_message')
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          showInlineNotification(data.message, 'success');
          form.reset();
        } else {
          showInlineNotification(data.message || 'Failed to send message', 'error');
        }
      } catch (error) {
        showInlineNotification('Network error. Please check your connection and try again.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      
      return false;
    }
    
    // Subscribe Form Handler
    async function handleSubscribeSubmit(event) {
      event.preventDefault();
      console.log('Subscribe form submitted via inline handler');
      
      const form = event.target;
      const formData = new FormData(form);
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="text text-1">Subscribing...</span>';
      
      try {
        const response = await fetch('api/subscribe.php', {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          showInlineNotification(data.message, 'success');
          form.reset();
        } else {
          showInlineNotification(data.message || 'Failed to subscribe', 'error');
        }
      } catch (error) {
        showInlineNotification('Network error. Please check your connection and try again.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
      
      return false;
    }
    
    // Inline Notification Function
    function showInlineNotification(message, type = 'info') {
      // Remove existing notifications
      const existingNotif = document.querySelector('.inline-notification');
      if (existingNotif) {
        existingNotif.remove();
      }
      
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `inline-notification ${type}`;
      notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
          <span style="font-size: 20px; font-weight: bold;">${type === 'success' ? '✓' : '✕'}</span>
          <span>${message}</span>
        </div>
      `;
      
      // Add styles
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
      `;
      
      document.body.appendChild(notification);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 5000);
    }
    
    // Add animation styles
    if (!document.getElementById('inline-notification-styles')) {
      const style = document.createElement('style');
      style.id = 'inline-notification-styles';
      style.textContent = `
        @keyframes slideIn {
          from {
            transform: translateX(400px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
      `;
      document.head.appendChild(style);
    }
    
    console.log('Inline form handlers loaded');
  </script>

</body>

</html>
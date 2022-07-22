 <?php include "inc/header.php" ?>
 <h1>Welcome</h1>

 <?php if (isset($_SESSION['email'])) : ?>

     <?php create_post(); ?>

     <br>
     <form method="POST">
         <h3>Create new post</h3>
         <textarea name="post_content" cols="60" rows="10" placeholder="Post content..."></textarea>
         <input type="submit" value="Post" name="submit">

     </form>

     <div>
         <?php display_message(); ?>
     </div>

     <hr>

     <div class="posts">
         <?php fetch_all_posts(); ?>
     </div>

 <?php else : ?>
     <div class="homepage">
         <h1>Welcome to Blex Socail Network</h1>
         <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. In harum, modi animi, dolor impedit unde aut quam praesentium iure minima cumque dolores nihil! Illum facilis, dolorum reprehenderit deserunt deleniti cumque.</p>

         <h2>Click <a href="login.php">here</a> to login!</h2>
         <img src="css/img/social.jpg" alt="">
     </div>

 <?php endif; ?>





 <!-- <?php
        if (isset($_SESSION['email'])) {
            echo "your email is " . $_SESSION['email'];
        } else {
            echo "Plese, log in!";
        }
        ?> -->

 <?php include "inc/footer.php" ?>
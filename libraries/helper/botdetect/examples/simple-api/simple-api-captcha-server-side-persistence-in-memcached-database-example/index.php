<?php require("lib/simple-botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Options: Server Side Persistence in Memcached Database Code Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Options: <br /> Server Side Persistence in Memcached Database Code Example</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

      <?php
        if (class_exists('Memcached')) {
          // Adding BotDetect Captcha to the page
          $MemcachedCaptchaExample = new SimpleCaptcha("MemcachedCaptchaExample");
          echo $MemcachedCaptchaExample->Html();
      ?>

      <div class="validationDiv">
        <input name="CaptchaCode" type="text" id="CaptchaCode" />
        <input type="submit" name="ValidateCaptchaButton" value="Validate" id="ValidateCaptchaButton" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $MemcachedCaptchaExample->Validate();

            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo "<span class=\"incorrect\">Incorrect code</span>";
            } else {
              // Captcha validation passed, perform protected action
              echo "<span class=\"correct\">Correct code</span>";
            }
          }
        ?>
      </div>
      <?php
        } else {
            echo "<div class='note warning'>Class Not Found: Memcached. You need to install Memcached first</div>";
        }
      ?>
    </fieldset>
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
          <p>The BotDetect PHP Simple Captcha options: Server side persistence in Memcached database code example shows how to use Memcached to store persist Captcha data instead of using default BotDetect Sqlite Database persistence provider.</p>
          <p>As you may know <a target="_blank" href="https://memcached.org/">Memcached</a> is an open source, in-memory data structure store used to improve data performance. Therefore Captcha data storage via Memcached is neccessary in real world. This combination makes your application have better performance. </p>
          <p>To use Memcached to store persist Captcha data, BotDetect Php Simple Captcha provides you a simple way by declaring Memcached configuration info in <code>lib/config/botdetect.xml</code> file, such as: memcached host, port.</p>
          <p>BotDetect PHP Simple Captcha uses <a href="http://php.net/manual/en/book.memcached.php" target="_blank">Memcached</a> as Memcached php client, so you need to ensure <a href="http://php.net/manual/en/book.memcached.php" target="_blank">Memcached</a> is installed.</p>
      </div>
    </div>
    
    <div class="column">
      <?php if (SimpleCaptcha::IsFree()) { ?>
      <div class="note warning">
        <h3>Free Version Limitations</h3>
        <ul>
          <li>The free version of BotDetect only includes a limited subset of the available CAPTCHA image styles and CAPTCHA sound styles.</li>
          <li>The free version of BotDetect includes a randomized <code>BotDetect™</code> trademark in the background of 50% of all Captcha images generated.</li>
          <li>It also has limited sound functionality, replacing the CAPTCHA sound with "SOUND DEMO" for randomly selected 50% of all CAPTCHA codes.</li>
          <li>Lastly, the bottom 10 px of the CAPTCHA image are reserved for a link to the BotDetect website.</li>
        </ul>
        <p>These limitations are removed if you <a rel="nofollow" href="http://captcha.com/shop.html?utm_source=installation&amp;utm_medium=php&amp;utm_campaign=4.0.0" title="BotDetect CAPTCHA online store, pricing information, payment options, licensing &amp; upgrading">upgrade</a> your BotDetect license.</p>
      </div>
      <?php } ?>
    </div>
  </div>
  
  <div id="systeminfo">
    <p><?php echo SimpleCaptcha::LibInfo(); ?></p>
  </div>
  
</body>
</html>
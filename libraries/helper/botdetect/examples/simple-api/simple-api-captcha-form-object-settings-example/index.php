<?php require("lib/simple-botdetect.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
  <title>BotDetect PHP CAPTCHA Options: Form Object Settings Code Example</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link type="text/css" rel="Stylesheet" href="stylesheet.css" />
  <link type="text/css" rel="stylesheet" href="lib/simple-botdetect.php?get=layout-stylesheet" />
</head>
<body>
  <form method="post" action="" class="column" id="form1">

    <h1>BotDetect PHP CAPTCHA Options: <br /> Form Object Settings Code Example</h1>

    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

       <?php // Adding BotDetect Captcha to the page
        $Captcha1 = new SimpleCaptcha("Captcha1");
        echo $Captcha1->Html();
      ?>

      <div class="validationDiv">
        <input name="CaptchaCode1" type="text" id="CaptchaCode1" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $Captcha1->Validate();

            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo '<span class="incorrect">Incorrect code</span>';
            } else {
              // Captcha validation passed, perform protected action
              echo '<span class="correct">Correct code</span>';
            }
          }
        ?>
      </div>

    </fieldset>


    <fieldset>
      <legend>PHP CAPTCHA validation</legend>
      <label for="CaptchaCode">Retype the characters from the picture:</label>

      <?php // Adding BotDetect Captcha to the page
        $Captcha2 = new SimpleCaptcha("Captcha2");
        echo $Captcha2->Html();
      ?>

      <div class="validationDiv">
        <input type="text" name="CaptchaCode2" id="CaptchaCode2" />

        <?php // when the form is submitted
          if ($_POST) {
            // validate the Captcha to check we're not dealing with a bot
            $isHuman = $Captcha2->Validate();

            if (!$isHuman) {
              // Captcha validation failed, show error message
              echo '<span class="incorrect">Incorrect code</span>';
            } else {
              // Captcha validation passed, perform protected action
              echo '<span class="correct">Correct code</span>';
            }
          }
        ?>
      </div>

    </fieldset>

    <input type="submit" name="SubmitButton" id="SubmitButton" value="Submit Form" />
  </form>

  <div class="column">
    <div class="column">
      <div class="note">
        <h3>CAPTCHA Code Example Description</h3>
        <p>This BotDetect Captcha PHP code example shows how to configure Captcha challenges by setting Captcha options in <code>lib/config/botdetect.xml</code> configuration file.</p>
        <p>Multiple PHP forms within the same PHP website can be protected by BotDetect Captcha challenges: e.g. you could include <code>simple-botdetect.php</code> in both your Contact form and Registration form source.</p>
        <p>To function properly, separate Captcha challenges placed on each form should have different names (<code>CaptchaId</code> values sent to the <code>Captcha</code> object constructor, <code>Captcha1</code> and <code>Captcha2</code> in this example), and can use completely different Captcha settings.</p>
        <p>Even multiple Captcha instances placed on the same form won't interfere with each other's validation and functionality. And if a user opens the same page in multiple browser tabs, each tab will independently validate the shown Captcha code.</p>
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
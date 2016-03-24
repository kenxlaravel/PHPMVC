<?php

// Generate a link to the contact page.
$contactPage = new Page('contact-us');

?>

<div class="help-term low-price-header">
    <div class="span-3"><img src="/new_images/low-price.png" alt="Low Price Guarantee"></div>
    <div class="span-10">
        <p class="h2">Low Price Guarantee</p>
        <p>SafetySign.com guarantees that we have the lowest price on every item we manufacture, plain and simple.</p>
        <p class="h4">If you find a lower price, we’ll match it!</p>
    </div>
</div>
<div class="help-term low-price-info">
    <p class="h2">How it works:</p>
    <p>Found the exact same product elsewhere for less? Get the same price from SafetySign.com either before or after you order.</p>
</div>
<div class="low-price-option prepend-top">
    <p class="h4 append-bottom"><span>Option 1:</span> Let us know at the time of purchase.</p>
    <p>During checkout, insert a message at the bottom of the page and include a link to the exact same product from a competitor’s website.</p>
    <img class="append-bottom" src="/new_images/low-price-screenshot.jpg" alt="checkout page screenshot">
    <p>We will confirm that the product is the same and once verified, we will adjust the price on your order and send you a confirmation email. Should your price match be denied, you will receive an email explaining why.</p>
</div>
<div class="low-price-option">
    <p class="h4 append-bottom"><span>Option 2:</span> Let us know within five (5) business days of your purchase.</p>
    <p><a class="underline bold" href="<?php echo $contactPage->getUrl(); ?>">Send us an email</a> that includes the following information:</p>
    <ul class="with-bullets append-bottom">
        <li>Your name</li>
        <li>Your company name (if applicable)</li>
        <li>ZIP code</li>
        <li>Telephone number</li>
        <li>Your SafetySign.com sales order number (e.g. SS123456789)</li>
        <li>A link to the exact same item from a competitor’s website</li>
    </ul>
    <p>If confirmed, a credit equal to the price difference will be made within five (5) business days.</p>
</div>
<div class="help-term prepend-top append-bottom">
    <p>Challenges submitted after five (5) business days of purchase from SafetySign.com will not be considered for credit. Offer extends exclusively to SafetySign.com customers.</p>
</div>
SETUP
1. Import schema.sql in phpMyAdmin (creates DB "buildmart" + sample products).
2. Edit DB_USER / DB_PASS in config.php.
3. Copy the folder to your PHP host (PHP 8+, XAMPP/cPanel), open index.php.
4. Create your account on auth.php, then in phpMyAdmin run:
   UPDATE users SET role='admin' WHERE email='your@email.com';
PAYMENTS: customers pay Mobile Money to 0783310479 and enter the transaction ID; confirm it in Admin > Orders (set status "paid").
SUPPORT EMAIL: thedonofficiall11@gmail.com (order alerts use PHP mail(); configure SMTP on your host).

UPGRADE (existing install): import upgrade.sql once, then upload all files.
ROLES: customer (buy, review) | seller (sell, needs admin approval) | support (staff desk: orders + chat) | admin (everything, Users page).
BUYING FLOW: cart > delivery & coupon > Mobile Money payment (0783310479) > review > order with tracking timeline. Customers can cancel pending orders; only verified buyers can review.

UPGRADE 2: import upgrade2.sql once. Make sure the "uploads" folder is writable (chmod 755/775).
SHOPS: sellers (approved by admin) add products with photos, set shop profile + district in Seller hub; public page shop.php?id=.
LOCATION: buyers pick a district in the top bar (saved to their profile). Recommendations rank: same district > same province > best sellers.

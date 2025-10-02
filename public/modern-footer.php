</div> <!-- Close container from header -->

    <footer class="footer mt-5">
        <div class="container">
            <div class="footer-top">
                <div>
                    <h3 class="footer-title">MyShop</h3>
                    <p>Your one-stop destination for quality products at affordable prices. Shop with confidence and enjoy a seamless shopping experience.</p>
                    <div class="mt-3">
                        <a href="#" style="color: #adb5bd; margin-right: 15px;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="color: #adb5bd; margin-right: 15px;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: #adb5bd; margin-right: 15px;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: #adb5bd;"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="/Ecomme/public/index.php">Home</a></li>
                        <li class="footer-link"><a href="/Ecomme/public/index.php">Products</a></li>
                        <li class="footer-link"><a href="/Ecomme/public/cart.php">Cart</a></li>
                        <li class="footer-link"><a href="/Ecomme/public/wishlist.php">Wishlist</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="footer-title">Account</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="/Ecomme/public/login.php">Login</a></li>
                        <li class="footer-link"><a href="/Ecomme/public/register.php">Register</a></li>
                        <li class="footer-link"><a href="/Ecomme/public/dashboard.php">My Account</a></li>
                        <li class="footer-link"><a href="/Ecomme/public/checkout.php">Checkout</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="footer-title">Contact Us</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><i class="fas fa-map-marker-alt"></i> No 10 , chennai , Tamil Nadu , 600096</li>
                        <li class="footer-link"><i class="fas fa-phone"></i> +91 9876543219</li>
                        <li class="footer-link"><i class="fas fa-envelope"></i> admin@example.com</li>
                        
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Ecomme. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Add Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <!-- Add custom JavaScript -->
    <script>
        // Add any custom JavaScript functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any components that need JavaScript
            
            // Example: Add to cart animation
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            if (addToCartButtons) {
                addToCartButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        // You can add animation or other functionality here
                        button.innerHTML = 'Added to Cart <i class="fas fa-check"></i>';
                        setTimeout(() => {
                            button.innerHTML = 'Add to Cart';
                        }, 2000);
                    });
                });
            }
        });
    </script>
</body>
</html>
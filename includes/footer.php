</main> <footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            
            <div class="footer-about">
                <h3>UPH FESTIVAL MUSIC</h3>
                <p>Platform festival musik digital terbesar yang merayakan kreativitas generasi muda. Temukan musisi favoritmu, nikmati seni, dan rasakan pengalaman tak terlupakan.</p>
                <div class="social-links">
                    <a href="#" target="_blank">Instagram</a>
                    <a href="#" target="_blank">TikTok</a>
                    <a href="#" target="_blank">Twitter</a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Menu Cepat</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Jadwal & Lineup</a></li>
                    <li><a href="explore.php">Beli Tiket</a></li>
                    <li><a href="gallery.php">Galeri</a></li>
                    <li><a href="about.php">Tentang Kami</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Newsletter</h4>
                <p>Dapatkan info promo tiket dan lineup terbaru langsung di emailmu.</p>
                
                <form class="footer-form" onsubmit="event.preventDefault(); alert('Terima kasih! Anda telah berlangganan.');">
                    <input type="email" placeholder="Masukkan email Anda..." required>
                    <button type="submit">Langganan</button>
                </form>

                <div class="contact-info">
                    <p>📍 UPH Karawaci, Tangerang</p>
                    <p>📧 support@upfm.id</p>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> UPFM. All rights reserved.</p>
            <div class="legal-links">
                <a href="#">Kebijakan Privasi</a>
                <span class="separator">•</span>
                <a href="#">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="/upfm_web/assets/js/main.js"></script>

</body>
</html>
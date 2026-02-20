<?php
declare(strict_types=1);
?>

  <!-- ===================== FOOTER ===================== -->
  <footer class="py-12" style="background-color: #3B2314;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <p class="text-lg font-bold text-[#FFFBF5] mb-2" style="font-family: 'Bitter', serif;"><?php echo esc_html(fabrika62_opt_str('brand_name', 'Fabrika Ajándék')); ?></p>
        <p class="text-sm text-[#E8DCC8] mb-6"><?php echo esc_html(fabrika62_opt_str('footer_location', 'Szarvas, Magyarország')); ?></p>

        <div class="copper-divider max-w-[120px] mx-auto mb-6"></div>

        <!-- Social links -->
        <div class="flex justify-center gap-5 mb-6">
          <a href="<?php echo esc_url(fabrika62_opt_str('footer_facebook_url', '#')); ?>" class="text-[#B87333] hover:text-[#C9A84C] transition-colors" aria-label="Facebook">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <a href="<?php echo esc_url(fabrika62_opt_str('footer_instagram_url', '#')); ?>" class="text-[#B87333] hover:text-[#C9A84C] transition-colors" aria-label="Instagram">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
        </div>

        <p class="text-xs text-[#E8DCC8] opacity-60">&copy; 2025 Fabrika Ajándék. Minden jog fenntartva.</p>
      </div>
    </div>
  </footer>

  <!-- Back to top button -->
  <button id="back-to-top" class="back-to-top fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full flex items-center justify-center text-[#FFFBF5] shadow-lg transition-all duration-300 hover:scale-110 cursor-pointer" style="background: linear-gradient(135deg, #B87333, #C9A84C);" aria-label="Vissza a tetejére">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
  </button>

<?php wp_footer(); ?>
</body>
</html>

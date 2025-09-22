(function() {
  document.addEventListener('DOMContentLoaded', function() {
      const copyBtn = document.querySelector('.aiols-copy-shortlink');

      if (copyBtn) {
          copyBtn.addEventListener('click', function() {
              const shortlink = copyBtn.getAttribute('data-shortlink');

              // Copy using Clipboard API if available
              if (navigator.clipboard && window.isSecureContext) {
                  navigator.clipboard.writeText(shortlink).then(function() {
                      alert(aiols_js.message + ' ' + shortlink);
                  });
              } else {
                  // Fallback for older browsers
                  const tempInput = document.createElement('input');
                  tempInput.value = shortlink;
                  document.body.appendChild(tempInput);
                  tempInput.select();
                  document.execCommand('copy');
                  document.body.removeChild(tempInput);
                  alert(aiols_js.message + ' ' + shortlink);
              }
          });
      }
  });

  /**
	 * Admin JS for "Copy" and "Generate Shortlink" buttons
	 *
	 * @since 1.0
	 */
  document.addEventListener('click', function(e) {

	  // Copy button
	  if (e.target && e.target.classList.contains('aiols-copy')) {
	      e.preventDefault();
	      const link = e.target.dataset.link;
	      navigator.clipboard.writeText(link).then(() => {
	          e.target.textContent = 'Copied!';
	          setTimeout(() => { e.target.textContent = 'Copy'; }, 1500);
	      });
	  }

	  // Generate button
	  if (e.target && e.target.classList.contains('aiols-generate')) {
	      e.preventDefault();
	      const postId = e.target.dataset.post;
	      const button = e.target;
	      button.disabled = true;
	      button.textContent = aiols_js.generate_text; // localized text

	      fetch(aiols_js.ajax_url, {
	          method: 'POST',
	          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
	          body: 'action=aiols_generate_shortlink&nonce=' + aiols_js.nonce + '&post_id=' + postId
	      })
	      .then(res => res.json())
	      .then(data => {
	          if (data.success) {
	              const link = data.data.shortlink;
	              button.outerHTML = '<a href="'+link+'" target="_blank">'+link+'</a> <button type="button" class="button button-small aiols-copy" data-link="'+link+'">Copy</button>';
	          } else {
	              button.textContent = aiols_js.error_text;
	              alert(aiols_js.error_message + ': ' + data.data);
	          }
	      })
	      .catch(err => {
	          button.textContent = aiols_js.error_text;
	          alert(aiols_js.error_message + ': ' + err);
	      });
	  }

	});


})();

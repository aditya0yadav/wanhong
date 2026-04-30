/**
 * Copies text to the clipboard, with a fallback for insecure contexts (non-HTTPS/localhost).
 * @param {string} text - The text to copy.
 * @returns {Promise<boolean>} - Resolves to true if successful, false otherwise.
 */
export const copyText = async (text) => {
  if (!text) return false;
  console.log(text, "how can i help");

  // Try the modern Clipboard API first (secure contexts only)
  if (navigator.clipboard && navigator.clipboard.writeText) {
    try {
      await navigator.clipboard.writeText(text);
      return true;
    } catch (err) {
      console.error('Modern Clipboard API failed:', err);
    }
  }

  // Fallback to the classic execCommand('copy') for insecure contexts
  try {
    const textArea = document.createElement('textarea');
    textArea.value = text;

    // Ensure it's not visible or affecting layout, but still in the DOM and selectable
    textArea.setAttribute('readonly', ''); // Prevent keyboard on mobile
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    textArea.style.top = '0';
    textArea.style.opacity = '0';
    // Prevent zooming on focus (iOS)
    textArea.style.fontSize = '12pt';

    document.body.appendChild(textArea);

    // Modern selection approach
    const selection = window.getSelection();
    const range = document.createRange();

    textArea.focus();
    textArea.select();

    // Some mobile browsers need this more explicit method
    textArea.setSelectionRange(0, 999999);

    const successful = document.execCommand('copy');
    document.body.removeChild(textArea);

    if (successful) {
      console.log('[Clipboard] Fallback copy successful');
      return true;
    } else {
      console.warn('[Clipboard] document.execCommand failed, likely due to lost user gesture (async context).');
      // Last resort: Show a prompt for manual copy
      window.prompt('Browser blocked automatic copy. Please copy the link below manually (Ctrl+C / Cmd+C):', text);
      return false;
    }
  } catch (err) {
    console.error('Clipboard fallback failed entirely:', err);
    // Even if everything fails, show the prompt
    window.prompt('Copy failed. Please copy the link below manually:', text);
    return false;
  }
};

<?php

if (!function_exists('highlightKeyword')) {
    /**
     * Highlight matching keywords in a text string
     * 
     * @param string $text The text to search in
     * @param string $keyword The keyword(s) to highlight
     * @param string $highlightClass CSS class for highlighting (default: text-danger-emphasis)
     * @return string HTML with highlighted keywords
     */
    function highlightKeyword($text, $keyword, $highlightClass = 'text-danger')
    {
        // Return original text if either parameter is empty
        if (empty($text) || empty($keyword)) {
            return htmlspecialchars($text);
        }

        // Escape the original text for safety
        $escapedText = htmlspecialchars($text);
        
        // Split the keyword into individual words and clean them
        $keywords = array_filter(array_map('trim', explode(' ', $keyword)));
        
        if (empty($keywords)) {
            return $escapedText;
        }

        // Create a pattern for all keywords (case-insensitive)
        $patterns = [];
        foreach ($keywords as $word) {
            // Escape special regex characters in the keyword
            $escapedWord = preg_quote($word, '/');
            // Create word boundary pattern for whole word matching
            $patterns[] = '\b' . $escapedWord . '\b';
        }
        
        // Combine all patterns with OR operator
        $pattern = '/(' . implode('|', $patterns) . ')/ui';
        
        // Perform the replacement with highlighting
        $highlightedText = preg_replace_callback(
            $pattern,
            function ($matches) use ($highlightClass) {
                return '<span class="' . $highlightClass . '">' . $matches[0] . '</span>';
            },
            $escapedText
        );

        return $highlightedText;
    }
}
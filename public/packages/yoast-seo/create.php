<?php
echo '<script type="text/javascript" src="http://localhost/laravel/shop/public/packages/yoast-seo/example-b.js"></script>
<div id="input" class="form-container">
    <div id="inputForm" class="inputForm">
        <button type="button" id="refresh-analysis">Refresh!</button>
        <label for="locale">Locale</label>
        <input type="text" id="locale" name="locale" placeholder="en_US" />
        <label for="content">Text</label>
        <textarea id="content" name="content" placeholder="Start writing your text!"></textarea>
        <label for="focusKeyword">Focus keyword</label>
        <input type="text" id="focusKeyword" name="focusKeyword" placeholder="Choose a focus keyword" />
    </div>
    <form id="snippetForm" class="snippetForm">
        <label>Snippet Preview</label>
        <div id="snippet" class="output">

        </div>
    </form>
</div>
<div id="output-container" class="output-container">
    <p>This is what the page might look like on a Google search result page.</p>

    <p>Edit the SEO title and meta description by clicking the title and meta description!</p>
    <h2>SEO assessments</h2>
    <div id="output" class="output">

    </div>
    <h2>Content assessments</h2>
    <div id="contentOutput" class="output">

    </div>
</div>';
<?php

function showHTMLCode($html)
{
    return "<pre style='background:#111;color:#0f0;padding:10px;'>
<code>" . htmlspecialchars($html, ENT_QUOTES, 'UTF-8') . '</code>
</pre>';
}

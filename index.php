<?php

/**
 * Theme fallback template.
 *
 * Acorn định tuyến template hierarchy của WordPress sang file
 * resources/views/*.blade.php tương ứng, nên file này thường không được chạy.
 * Nó tồn tại để WordPress công nhận đây là theme hợp lệ, đồng thời render
 * view "index" như một phương án dự phòng.
 */

echo \Roots\view(app('sage.view'), app('sage.data'))->render();

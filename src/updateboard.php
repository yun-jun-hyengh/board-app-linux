<?php
    require_once 'config/configdb.php';

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM php_board WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        die("존재하지 않는 게시글입니다.");
    }

    // 현재 첨부된 파일이 이미지인지 확인하는 로직 
    $isImage = false;
    if($post['filepath']) {
        
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $writer = trim($_POST['writer']);
        $content = trim($_POST['content']);
        
        $filename = $post['filename'];
        $filepath = $post['filepath'];

        if (isset($_FILES['attached_file']) && $_FILES['attached_file']['error'] === UPLOAD_ERR_OK) {
            if ($filepath && file_exists(__DIR__ . '/' . $filepath)) {
                unlink(__DIR__ . '/' . $filepath);
            }
            $fileTmpPath = $_FILES['attached_file']['tmp_name'];
            $originalName = $_FILES['attached_file']['name'];
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            $newFileName = $uuid . '.' . $fileExtension;
            $dest_path = __DIR__ . '/uploads/' . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $filename = $originalName;
                $filepath = 'uploads/' . $newFileName;
            }
        }

        if ($title && $writer && $content) {
            $stmt = $pdo->prepare("UPDATE php_board SET title = ?, writer = ?, content = ?, filename = ?, filepath = ? WHERE id = ?");
            $stmt->execute([$title, $writer, $content, $filename, $filepath, $id]);
            header("Location: index.php");
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글 수정하기</title>
    <style>
        /* 기존 CSS 유지 */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Pretendard', sans-serif; }
        body { background-color: #f8fafc; color: #334155; display: flex; align-items: center; justify-content: center; height: 100vh; padding: 20px; }
        .edit-container { background: #fff; padding: 35px; border-radius: 16px; border: 1px solid #e2e8f0; max-width: 500px; width: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h2 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .subtitle { color: #94a3b8; font-size: 0.85rem; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; outline: none; }
        .current-file { font-size: 0.8rem; color: #64748b; margin-top: 5px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 12px; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; text-align: center; font-size: 0.9rem; text-decoration: none; }
        .btn-submit { background: #4f46e5; color: #fff; flex: 2; }
        .btn-cancel { background: #f1f5f9; color: #475569; flex: 1; }
    </style>
</head>
<body>

    <div class="edit-container">
        <h2>게시글 수정</h2>
        
        <form action="updateboard.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>작성자</label>
                <input type="text" name="writer" value="<?= htmlspecialchars($post['writer']) ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>제목</label>
                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>내용</label>
                <textarea name="content" rows="5" required class="form-control"><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div class="form-group">
                <label>첨부파일 수정</label>
                <input type="file" name="attached_file" class="form-control">
                <?php if ($post['filename']): ?>
                    <div class="current-file">현재 파일: <?= htmlspecialchars($post['filename']) ?></div>
                <?php endif; ?>
            </div>
            <div class="btn-group">
                <a href="index.php" class="btn btn-cancel">취소</a>
                <button type="submit" class="btn btn-submit">수정 완료</button>
            </div>
        </form>
    </div>

</body>
</html>
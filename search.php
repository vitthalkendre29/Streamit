<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$searchQuery = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$videosPerPage = 12;
$offset = ($page - 1) * $videosPerPage;

$categories = getCategories();
$videos = searchVideos($searchQuery, $categoryFilter, $videosPerPage, $offset);
$totalVideos = countSearchVideos($searchQuery, $categoryFilter);
$totalPages = ceil($totalVideos / $videosPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $searchQuery ? 'Search: ' . htmlspecialchars($searchQuery) : 'Browse Videos'; ?> - StreamIt</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="search-header">
            <h1 style="color: white; margin-bottom: 2rem;">
                <?php echo $searchQuery ? 'Search Results for "' . htmlspecialchars($searchQuery) . '"' : 'Browse Videos'; ?>
            </h1>
            
            <?php if ($searchQuery): ?>
                <p style="color: white; opacity: 0.8; margin-bottom: 2rem;">
                    Found <?php echo number_format($totalVideos); ?> video(s)
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Search & Filter Form -->
        <div class="search-filters">
            <form class="search-bar"  method="GET" action="search.php" class="filter-form">
                <input type="text" name="q" placeholder="Search videos..." value="<?php echo htmlspecialchars($searchQuery); ?>" class="search-input">
                    <select class="category-select" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"
                                    <?php echo ($categoryFilter == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" style="position: absolute;right: 15%;" class="btn btn-primary">Search</button>
            </form>
        </div>
        
        <!-- Videos Grid -->
        <?php if (empty($videos)): ?>
            <div class="no-results">
                <h3>No videos found</h3>
                <p>Try adjusting your search terms or browse all videos.</p>
                <a href="search.php" class="btn btn-primary">Browse All Videos</a>
            </div>
        <?php else: ?>
            <div class="video-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-card" onclick="location.href='video.php?id=<?php echo $video['id']; ?>'">
                        <div class="video-thumbnail">
                            <?php if ($video['thumbnail']): ?>
                                <img src="uploads/thumbnails/<?php echo htmlspecialchars($video['thumbnail']); ?>" 
                                     alt="<?php echo htmlspecialchars($video['title']); ?>">
                            <?php else: ?>
                                <div class="default-thumbnail">▶️</div>
                            <?php endif; ?>
                        </div>
                        <div class="video-info">
                            <div class="video-title"><?php echo htmlspecialchars($video['title']); ?></div>
                            <div class="video-meta">
                                <?php echo htmlspecialchars($video['username']); ?> • 
                                <?php echo timeAgo($video['upload_date']); ?> • 
                                <?php echo htmlspecialchars($video['category_name']); ?>
                            </div>
                            <div class="video-stats">
                                <?php echo number_format($video['views']); ?> views
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php
                    $queryParams = [];
                    if ($searchQuery) $queryParams['q'] = $searchQuery;
                    if ($categoryFilter) $queryParams['category'] = $categoryFilter;
                    $baseUrl = 'search.php?' . http_build_query($queryParams);
                    ?>
                    
                    <?php if ($page > 1): ?>
                        <a href="<?php echo $baseUrl; ?>&page=<?php echo ($page - 1); ?>" class="btn btn-secondary">← Previous</a>
                    <?php endif; ?>
                    
                    <div class="page-numbers">
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <?php if ($i == $page): ?>
                                <span class="current-page"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo $baseUrl; ?>&page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="<?php echo $baseUrl; ?>&page=<?php echo ($page + 1); ?>" class="btn btn-secondary">Next →</a>
                    <?php endif; ?>
                </div>
                
                <div class="pagination-info">
                    Showing <?php echo (($page - 1) * $videosPerPage + 1); ?> - 
                    <?php echo min($page * $videosPerPage, $totalVideos); ?> of 
                    <?php echo number_format($totalVideos); ?> videos
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2025 StreamIt. A secure, private video streaming platform.</p>
    </div>

    <script src="./js/main.js"></script>
</body>
</html> 
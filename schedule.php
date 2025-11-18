<?php
require_once 'includes/db.php';
$page_title = 'Jadwal Festival';
require_once 'includes/header.php';

// 1. Ambil semua data jadwal, gabungkan dengan nama Artis dan Panggung
$sql = "SELECT 
            s.event_day, 
            s.start_time, 
            s.end_time,
            a.name AS artist_name,
            st.name AS stage_name
        FROM 
            schedules s
        JOIN 
            artists a ON s.artist_id = a.id
        JOIN 
            stages st ON s.stage_id = st.id
        ORDER BY 
            s.event_day, st.name, s.start_time";
            
$raw_schedules = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// 2. Susun ulang data ke dalam array yang terkelompok
$grouped_schedule = [];
foreach ($raw_schedules as $schedule) {
    $day = $schedule['event_day'];
    $stage = $schedule['stage_name'];
    $grouped_schedule[$day][$stage][] = $schedule; // Masukkan ke grup
}
?>

<div class="container page-container">
    <h1 class="page-main-title">Jadwal Acara</h1>
    <p class="page-subtitle">Rencanakan pengalaman festivalmu. Jangan lewatkan satupun penampilan!</p>

    <div class="schedule-container">
        <?php if (!empty($grouped_schedule)): ?>
            
            <?php foreach ($grouped_schedule as $day => $stages): ?>
                <div class="schedule-day-group">
                    <h2 class="schedule-day-title">
                        <?php echo date('l, d F Y', strtotime($day)); ?>
                    </h2>

                    <?php foreach ($stages as $stage_name => $artists): ?>
                        <div class="schedule-stage-group">
                            <h3 class="schedule-stage-title"><?php echo htmlspecialchars($stage_name); ?></h3>
                            
                            <div class="schedule-item-list">
                                <?php foreach ($artists as $artist_performance): ?>
                                    <div class="schedule-item">
                                        <div class="schedule-time">
                                            <?php echo date('H:i', strtotime($artist_performance['start_time'])); ?> - 
                                            <?php echo date('H:i', strtotime($artist_performance['end_time'])); ?>
                                        </div>
                                        <div class="schedule-artist">
                                            <?php echo htmlspecialchars($artist_performance['artist_name']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p style="text-align: center;">Jadwal acara akan segera diumumkan. Mohon bersabar!</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
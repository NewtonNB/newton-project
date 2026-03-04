<?php
// Demo event data
$events = [
    1 => [
        'title' => 'Independence Day',
        'date' => '09 OCT 2025',
        'time' => '10:00 AM - 2:00 PM',
        'image' => 'nyabzgallery/indepe.jpg',
        'gallery' => ['nyabzgallery/indepe.jpg', 'nyabzgallery/current.jpg'],
        'location' => 'Nyabikoni Secondary School Grounds',
        'download' => '',
        'speakers' => [
            ['name' => 'Hon. Janet Kataaha Museveni', 'title' => 'Minister of Education', 'photo' => 'nyabzgallery/HEADMASTER.jpg'],
            ['name' => 'Mr. John Doe', 'title' => 'Headmaster']
        ],
        'description' => "Uganda will celebrate the 62nd anniversary of its independence hosted by the northern district of Kitgum, highlighting the nation's unity and progress."
    ],
    2 => [
        'title' => 'Induction Ceremony',
        'date' => '15 JUL 2025',
        'time' => '9:00 AM - 12:00 PM',
        'image' => 'nyabzgallery/ceremony.jpg',
        'gallery' => ['nyabzgallery/ceremony.jpg'],
        'location' => 'Main Hall',
        'download' => '',
        'speakers' => [
            ['name' => 'Ms. Jane Smith', 'title' => 'Senior Teacher']
        ],
        'description' => 'The school will hold an induction ceremony to officially welcome S.1 and S.5 learners, encouraging them to embrace the school community and its values.'
    ],
    3 => [
        'title' => 'Pre-Mock Examinations',
        'date' => '20 JUL 2025',
        'image' => 'nyabzgallery/exams.jpg',
        'gallery' => ['nyabzgallery/exams.jpg'],
        'location' => 'Examination Rooms',
        'download' => '',
        'description' => 'Learners in S.4 and S.6 will sit their pre-mock examinations, preparing them for the upcoming mock exams with confidence and focus.'
    ],
    4 => [
        'title' => 'Parent Visitation Day (Term III, 2025)',
        'date' => '10 AUG 2025',
        'image' => 'nyabzgallery/VD.jpg',
        'gallery' => ['nyabzgallery/VD.jpg'],
        'location' => 'School Compound',
        'download' => '',
        'description' => "Visitation day is scheduled for Sunday, 10 August 2025 starting at 8:00 AM. This is an opportunity to check on your child’s progress and the school's environment."
    ],
    5 => [
        'title' => 'UACE Results, 2025',
        'date' => '15 MAR 2025',
        'image' => 'nyabzgallery/sucsess2.jpg',
        'gallery' => ['nyabzgallery/sucsess2.jpg'],
        'location' => 'Ministry of Education',
        'download' => 'ADMISSION FORM FOR TERM I.docx',
        'description' => 'The Minister of Education and Sports, Hon. Janet Kataaha Museveni, will release the 2025 Uganda Advanced Certificate of Education results celebrating student achievements.'
    ],
    6 => [
        'title' => 'UCE Results, 2025',
        'date' => '22 MAR 2025',
        'image' => 'nyabzgallery/UCE RESULTS (2).jpg',
        'gallery' => ['nyabzgallery/UCE RESULTS (2).jpg'],
        'location' => 'State House',
        'download' => '',
        'description' => 'The Minister of Education and Sports, Hon. Janet Kataaha Museveni, will release the 2025 Uganda Certificate of Education results at State House, recognizing student excellence.'
    ],
    7 => [
        'title' => 'Chapel Times',
        'date' => '28 JUL 2025',
        'image' => 'nyabzgallery/chapel3.jpg',
        'gallery' => ['nyabzgallery/chapel3.jpg'],
        'location' => 'School Chapel',
        'download' => '',
        'description' => 'Students of Nyabikoni Secondary School and Jack & Jill Primary School will gather for the Thanksgiving Mass on the Thirteenth Sunday in Ordinary Time, fostering spiritual growth.'
    ],
    8 => [
        'title' => 'Parent Visitation Day (Term II, 2025)',
        'date' => '01 JUL 2025',
        'image' => 'nyabzgallery/VD.jpg',
        'gallery' => ['nyabzgallery/VD.jpg'],
        'location' => 'School Compound',
        'download' => '',
        'description' => 'Parents and guardians are invited to visitation day on Tuesday, 1 July 2025 starting at 8:00 AM to discuss their children’s welfare and school development.'
    ],
    9 => [
        'title' => 'School Life',
        'date' => '05 AUG 2025',
        'image' => 'nyabzgallery/school life.JPG',
        'gallery' => ['nyabzgallery/school life.JPG'],
        'location' => 'School Premises',
        'download' => '',
        'description' => 'At Nyabikoni Secondary School, students enjoy a safe, inclusive environment with wide curricular and co-curricular opportunities to support their development and success.'
    ],
];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = $events[$id] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event ? $event['title'] : 'Event Not Found'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #f4f8fb; margin: 0; }
        .event-details-container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 18px; box-shadow: 0 8px 32px rgba(25, 118, 210, 0.10); padding: 32px; }
        .event-title { font-family: 'Bungee', cursive; font-size: 2.2rem; color: #1976d2; margin-bottom: 12px; }
        .event-date { color: #fff; background: #1976d2; display: inline-block; padding: 8px 18px; border-radius: 20px; font-weight: 700; margin-bottom: 18px; font-size: 1.1rem; }
        .event-time { color: #1976d2; font-weight: 600; margin-bottom: 18px; display: block; }
        .event-image { width: 100%; max-height: 320px; object-fit: cover; border-radius: 12px; margin-bottom: 18px; }
        .event-description { font-size: 1.15rem; color: #333; margin-bottom: 24px; }
        .event-location { font-size: 1.05rem; color: #1976d2; margin-bottom: 18px; font-weight: 600; }
        .event-gallery { margin-bottom: 24px; }
        .event-gallery img { width: 110px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 8px; margin-bottom: 8px; border: 2px solid #e3eafc; }
        .event-download { display: inline-block; margin-bottom: 18px; color: #fff; background: #1976d2; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .event-download:hover { background: #1251a2; }
        .event-speakers { margin-bottom: 24px; }
        .event-speaker { display: flex; align-items: center; margin-bottom: 10px; }
        .event-speaker-photo { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-right: 12px; border: 2px solid #e3eafc; }
        .event-speaker-info { font-size: 1rem; color: #222; }
        .event-speaker-name { font-weight: 600; }
        .event-speaker-title { font-size: 0.95em; color: #1976d2; }
        .back-link { display: inline-block; margin-top: 18px; color: #1976d2; text-decoration: none; font-weight: 600; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="event-details-container">
        <?php if ($event): ?>
            <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
            <div class="event-date"><?php echo htmlspecialchars($event['date']); ?></div>
            <?php if (!empty($event['time'])): ?>
                <span class="event-time"><i class="fa fa-clock"></i> <?php echo htmlspecialchars($event['time']); ?></span>
            <?php endif; ?>
            <img class="event-image" src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
            <?php if (!empty($event['location'])): ?>
                <div class="event-location"><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></div>
            <?php endif; ?>
            <div class="event-description"><?php echo htmlspecialchars($event['description']); ?></div>
            <?php if (!empty($event['speakers'])): ?>
                <div class="event-speakers">
                    <strong>Speakers:</strong><br>
                    <?php foreach ($event['speakers'] as $sp): ?>
                        <div class="event-speaker">
                            <?php if (!empty($sp['photo'])): ?>
                                <img class="event-speaker-photo" src="<?php echo htmlspecialchars($sp['photo']); ?>" alt="<?php echo htmlspecialchars($sp['name']); ?>">
                            <?php endif; ?>
                            <div class="event-speaker-info">
                                <span class="event-speaker-name"><?php echo htmlspecialchars($sp['name']); ?></span>
                                <?php if (!empty($sp['title'])): ?>
                                    <span class="event-speaker-title"> - <?php echo htmlspecialchars($sp['title']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($event['gallery'])): ?>
                <div class="event-gallery">
                    <strong>Gallery:</strong><br>
                    <?php foreach ($event['gallery'] as $img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Gallery image">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($event['download'])): ?>
                <a class="event-download" href="<?php echo htmlspecialchars($event['download']); ?>" download>Download File</a>
            <?php endif; ?>
            <a class="back-link" href="events.php">&larr; Back to Events</a>
        <?php else: ?>
            <div class="event-title">Event Not Found</div>
            <div class="event-description">Sorry, the event you are looking for does not exist.</div>
            <a class="back-link" href="events.php">&larr; Back to Events</a>
        <?php endif; ?>
    </div>
</body>
</html> 
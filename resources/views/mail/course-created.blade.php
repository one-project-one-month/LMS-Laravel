{{-- 
<!DOCTYPE html>
<html>
<head>
    <title>New Course Approval Request</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .btn { display: inline-block; padding: 10px 20px; margin-top: 10px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body> --}}

<div class="container">
    <h2>New Course Approval Request</h2>
    <p><strong>Instructor:</strong> {{ $user->username ?? 'instructor Name' }}</p>
    <p><strong>Course Title:</strong> {{ $course->course_name ?? 'course name' }}</p>
    <p><strong>Description:</strong> {{ $course->description ?? 'description' }}</p>

    <p>Please review the course and approve or reject it.</p>

    <a href="{{ url('/courses/publish/' . $course->id) }}" class="btn">Approve Course</a>
    <a href="{{ url('/courses/unpublish/' . $course->id) }}" class="btn" style="background-color: red;">Reject
        Course</a>
</div>
{{-- 
</body>
</html> --}}

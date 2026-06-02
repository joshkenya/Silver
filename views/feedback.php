<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Silver Entertainment</title>
    <link rel="stylesheet" href="/silver/public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="feedback-form-container">
            <h2>Send us your Feedback</h2>
            <form id="feedbackForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="feedback_type">Feedback Type</label>
                    <select id="feedback_type" name="feedback_type" required>
                        <option value="general">General</option>
                        <option value="bug">Bug Report</option>
                        <option value="feature_request">Feature Request</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <div class="form-group">
                    <label for="images">Attach Images (Max 3)</label>
                    <input type="file" id="images" name="images" multiple accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('images');
            if (fileInput.files.length > 3) {
                alert('Maximum 3 images allowed');
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'submit_feedback');
            
            fetch('/silver/public/api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Feedback submitted successfully! Thank you for your input.');
                    document.getElementById('feedbackForm').reset();
                } else {
                    alert(data.message);
                }
            });
        });
    </script>
</body>
</html>

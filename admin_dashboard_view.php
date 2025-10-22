<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Faculty Evaluation System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="wesleyan-university-philippines-cabanatuan-city-logo-removebg-preview.png" alt="wesleyan-logo">
            </div>
            <h1>Faculty Evaluation System</h1>
            <p>Comprehensive Performance Assessment & Feedback Management</p>
            <div style="text-align: right; margin-top: 10px;">
                <span style="color: #2d5016;">Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong> (Admin)</span> | 
                <a href="logout.php" style="color: #011002ff; text-decoration: none; font-weight: bold;">Logout</a>
            </div>
        </header>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showSection('dashboard')">Dashboard</button>
            <button class="nav-tab" onclick="showSection('faculty')">Manage Faculty</button>
            <button class="nav-tab" onclick="showSection('questions')">Manage Questions</button>
            <button class="nav-tab" onclick="showSection('evaluations')">View Evaluations</button>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Dashboard Section -->
        <div id="dashboard" class="content-section active">
            <h2>Admin Dashboard</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $total_evaluations; ?></h3>
                    <p>Total Evaluations</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_faculty; ?></h3>
                    <p>Active Faculty Members</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $avg_rating; ?></h3>
                    <p>Average Rating</p>
                </div>
            </div>
        </div>

        <!-- Manage Faculty Section -->
        <div id="faculty" class="content-section">
            <h2>Manage Faculty Members</h2>
            
            <button class="btn" onclick="openAddFacultyModal()" style="margin-bottom: 20px;">+ Add New Faculty</button>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($faculty = $faculty_list->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $faculty['id']; ?></td>
                            <td><?php echo htmlspecialchars($faculty['name']); ?></td>
                            <td><?php echo htmlspecialchars($faculty['email']); ?></td>
                            <td><?php echo htmlspecialchars($faculty['department']); ?></td>
                            <td><span style="color: <?php echo $faculty['status'] === 'active' ? 'green' : 'red'; ?>;"><?php echo ucfirst($faculty['status']); ?></span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-small" onclick='openEditFacultyModal(<?php echo json_encode($faculty); ?>)'>Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this faculty member?');">
                                        <input type="hidden" name="faculty_id" value="<?php echo $faculty['id']; ?>">
                                        <button type="submit" name="delete_faculty" class="btn btn-small btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manage Questions Section -->
        <div id="questions" class="content-section">
            <h2>Manage Evaluation Questions</h2>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="new-question">Add New Question</label>
                <form method="POST">
                    <textarea id="new-question" name="question_text" placeholder="Enter new evaluation question..." required></textarea>
                    <button type="submit" name="add_question" class="btn" style="margin-top: 10px;">Add Question</button>
                </form>
            </div>

            <h3>Existing Questions</h3>
            <div class="question-list">
                <?php while ($question = $questions_list->fetch_assoc()): ?>
                <div class="question-item">
                    <strong>Question <?php echo $question['question_order']; ?>:</strong> 
                    <?php echo htmlspecialchars($question['question_text']); ?>
                    <span style="margin-left: 10px; color: <?php echo $question['is_active'] ? 'green' : 'red'; ?>;">
                        (<?php echo $question['is_active'] ? 'Active' : 'Inactive'; ?>)
                    </span>
                    <div class="action-buttons" style="margin-top: 10px;">
                        <button class="btn btn-small" onclick='openEditQuestionModal(<?php echo json_encode($question); ?>)'>Edit</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                            <button type="submit" name="delete_question" class="btn btn-small btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- View Evaluations Section -->
        <div id="evaluations" class="content-section">
            <h2>View Evaluation Results</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Department</th>
                            <th>Faculty</th>
                            <th>Subject</th>
                            <th>Avg Rating</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($eval = $evaluations->fetch_assoc()): 
                            $avg = ($eval['question1'] + $eval['question2'] + $eval['question3']) / 3;
                        ?>
                        <tr>
                            <td><?php echo $eval['id']; ?></td>
                            <td><?php echo htmlspecialchars($eval['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($eval['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($eval['department']); ?></td>
                            <td><?php echo htmlspecialchars($eval['faculty_name']); ?></td>
                            <td><?php echo htmlspecialchars($eval['subject']); ?></td>
                            <td><?php echo number_format($avg, 1); ?></td>
                            <td><?php echo date('M d, Y', strtotime($eval['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-small" onclick='viewEvaluationDetails(<?php echo json_encode($eval); ?>)'>View</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Faculty Modal -->
    <div id="addFacultyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addFacultyModal')">&times;</span>
            <h2>Add New Faculty Member</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Faculty Name</label>
                    <input type="text" name="faculty_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="faculty_email" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="faculty_department" required>
                        <option value="">Select Department</option>
                        <option value="CECT">CECT - College of Engineering and Computer Technology</option>
                        <option value="CCJE">CCJE - College of Criminal Justice Education</option>
                        <option value="CON">CON - College of Nursing</option>
                        <option value="CBA">CBA - College of Business Administration</option>
                        <option value="CHTM">CHTM - College of Hospitality and Tourism Management</option>
                        <option value="CAS">CAS - College of Arts and Sciences</option>
                        <option value="COED">COED - College of Education</option>
                    </select>
                </div>
                <button type="submit" name="add_faculty" class="btn">Add Faculty</button>
            </form>
        </div>
    </div>

    <!-- Edit Faculty Modal -->
    <div id="editFacultyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editFacultyModal')">&times;</span>
            <h2>Edit Faculty Member</h2>
            <form method="POST">
                <input type="hidden" name="faculty_id" id="edit_faculty_id">
                <div class="form-group">
                    <label>Faculty Name</label>
                    <input type="text" name="faculty_name" id="edit_faculty_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="faculty_email" id="edit_faculty_email" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="faculty_department" id="edit_faculty_department" required>
                        <option value="CECT">CECT</option>
                        <option value="CCJE">CCJE</option>
                        <option value="CON">CON</option>
                        <option value="CBA">CBA</option>
                        <option value="CHTM">CHTM</option>
                        <option value="CAS">CAS</option>
                        <option value="COED">COED</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="faculty_status" id="edit_faculty_status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="update_faculty" class="btn">Update Faculty</button>
            </form>
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div id="editQuestionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editQuestionModal')">&times;</span>
            <h2>Edit Question</h2>
            <form method="POST">
                <input type="hidden" name="question_id" id="edit_question_id">
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea name="question_text" id="edit_question_text" required></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" id="edit_question_status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="update_question" class="btn">Update Question</button>
            </form>
        </div>
    </div>

    <!-- View Evaluation Modal -->
    <div id="viewEvaluationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewEvaluationModal')">&times;</span>
            <h2>Evaluation Details</h2>
            <div id="evaluationDetails"></div>
        </div>
    </div>

    <script src="admin_scripts.js"></script>
</body>
</html>
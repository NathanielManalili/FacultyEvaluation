// Tab Navigation
function showSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.remove('active'));
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.nav-tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Show selected section
    document.getElementById(sectionId).classList.add('active');
    
    // Add active class to clicked tab
    event.target.classList.add('active');
}

// Faculty Modal Functions
function openAddFacultyModal() {
    document.getElementById('addFacultyModal').style.display = 'block';
}

function openEditFacultyModal(faculty) {
    document.getElementById('edit_faculty_id').value = faculty.id;
    document.getElementById('edit_faculty_name').value = faculty.name;
    document.getElementById('edit_faculty_email').value = faculty.email;
    document.getElementById('edit_faculty_department').value = faculty.department;
    document.getElementById('edit_faculty_status').value = faculty.status;
    document.getElementById('editFacultyModal').style.display = 'block';
}

// Question Modal Functions
function openEditQuestionModal(question) {
    document.getElementById('edit_question_id').value = question.id;
    document.getElementById('edit_question_text').value = question.question_text;
    document.getElementById('edit_question_status').value = question.is_active;
    document.getElementById('editQuestionModal').style.display = 'block';
}

// Evaluation Details Modal
function viewEvaluationDetails(evaluation) {
    const details = `
        <p><strong>Student Name:</strong> ${evaluation.student_name}</p>
        <p><strong>Student ID:</strong> ${evaluation.student_id}</p>
        <p><strong>Department:</strong> ${evaluation.department}</p>
        <p><strong>Faculty:</strong> ${evaluation.faculty_name}</p>
        <p><strong>Subject:</strong> ${evaluation.subject}</p>
        <hr>
        <p><strong>Question 1 Rating:</strong> ${evaluation.question1}/5</p>
        <p><strong>Question 2 Rating:</strong> ${evaluation.question2}/5</p>
        <p><strong>Question 3 Rating:</strong> ${evaluation.question3}/5</p>
        <hr>
        <p><strong>Comments:</strong></p>
        <p>${evaluation.comments || 'No comments provided'}</p>
    `;
    document.getElementById('evaluationDetails').innerHTML = details;
    document.getElementById('viewEvaluationModal').style.display = 'block';
}

// Close Modal Function
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
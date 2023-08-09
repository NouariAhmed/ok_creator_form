
// Get references to the select elements
const bookTypeSelect = document.getElementById('book_type');
const bookLevelSelect = document.getElementById('book_level');
const subjectSelect = document.getElementById('subject');

// Disable the Book Level and Subject selects by default
bookLevelSelect.disabled = true;
subjectSelect.disabled = true;

// Event listener to handle Book Type selection
bookTypeSelect.addEventListener('change', function () {
const selectedBookType = bookTypeSelect.value;
// If no book type is selected, clear and disable the Book Level and Subject selects
if (selectedBookType === '') {
clearBookLevelAndSubject();
} else {
// Fetch book levels based on the selected book type from the server using Ajax
fetchBookLevels(selectedBookType);
}
});

// Event listener to handle Book Level selection
bookLevelSelect.addEventListener('change', function () {
const selectedBookLevel = bookLevelSelect.value;
// If no book level is selected, disable the Subject select and show appropriate message
if (selectedBookLevel === '') {
clearSubject();
} else {
// Fetch subjects based on the selected book level from the server using Ajax
fetchSubjects(selectedBookLevel);
}
});

// Function to fetch book levels using Ajax
function fetchBookLevels(bookType) {
fetch('../get_book_levels.php?type_id=' + bookType)
.then(response => response.json())
.then(data => {
// Generate the Book Level select options
const bookLevelsOptions = data.map(level => `<option value="${level.id}">${level.level_name}</option>`);
// Display the Book Level select
bookLevelSelect.innerHTML = '<option value="">Select Book Level</option>' + bookLevelsOptions.join('');
// Enable the Book Level select
bookLevelSelect.disabled = false;
// Clear and disable the Subject select
clearSubject();
})
.catch(error => console.error('Error fetching book levels:', error));
}

// Function to fetch subjects using Ajax
function fetchSubjects(bookLevel) {
fetch('../get_subjects.php?level_id=' + bookLevel)
.then(response => response.json())
.then(data => {
// Generate the Subject select options
const subjectsOptions = data.map(subject => `<option value="${subject.id}">${subject.subject_name}</option>`);
// Display the Subject select
subjectSelect.innerHTML = '<option value="">Select Subject</option>' + subjectsOptions.join('');
// Enable the Subject select
subjectSelect.disabled = false;
})
.catch(error => console.error('Error fetching subjects:', error));
}

// Function to clear and disable the Subject select
function clearSubject() {
subjectSelect.innerHTML = '<option value="">Select Subject</option>';
subjectSelect.disabled = true;
}

// Function to clear and disable the Book Level and Subject selects
function clearBookLevelAndSubject() {
bookLevelSelect.innerHTML = '<option value="">Select Book Level</option>';
bookLevelSelect.disabled = true;
clearSubject();
}

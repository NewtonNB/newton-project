// subjects-loader.js - Dynamic subject loader for HTML files
class SubjectsLoader {
    constructor(containerId, level) {
        this.container = document.getElementById(containerId);
        this.level = level;
        this.apiUrl = `get_subjects.php?level=${level}`;
    }

    async loadSubjects() {
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            
            if (data.success && data.subjects.length > 0) {
                this.renderSubjects(data.subjects);
            } else {
                this.renderNoSubjects();
            }
        } catch (error) {
            console.error('Error loading subjects:', error);
            this.renderError();
        }
    }

    renderSubjects(subjects) {
        let html = '';
        let delay = 1;
        
        subjects.forEach((subject, index) => {
            const delayClass = delay <= 6 ? `animate__delay-${delay}s` : '';
            const cardClass = this.level === 'olevel' ? 'subject-card' : 'course';
            
            html += `
                <div class="${cardClass} wow animate__animated animate__fadeInUp ${delayClass}">
                    <h3>${this.escapeHtml(subject.subject_name)}</h3>
                    <p>${this.getSubjectDescription(subject.subject_name)}</p>
                </div>
            `;
            
            delay = (delay >= 6) ? 1 : delay + 1;
        });
        
        this.container.innerHTML = html;
    }

    renderNoSubjects() {
        const icon = this.level === 'olevel' ? 'fa-book' : 'fa-university';
        const title = this.level === 'olevel' ? 'No O-Level Subjects Available' : 'No A-Level Subjects Available';
        
        this.container.innerHTML = `
            <div class="no-subjects">
                <i class="fa ${icon}"></i>
                <h3>${title}</h3>
                <p>Subjects will be displayed here once they are added to the system.</p>
            </div>
        `;
    }

    renderError() {
        this.container.innerHTML = `
            <div class="no-subjects">
                <i class="fa fa-exclamation-triangle"></i>
                <h3>Error Loading Subjects</h3>
                <p>Unable to load subjects at this time. Please try again later.</p>
            </div>
        `;
    }

    getSubjectDescription(subjectName) {
        const descriptions = {
            'ENGLISH': 'English Language and Grammar',
            'LITERATURE IN ENGLISH': 'Analysis of English Literature',
            'CHRISTIAN RELIGIOUS EDUCATION': 'Study of Christian Values',
            'HISTORY': 'World and Local History',
            'GEOGRAPHY': 'Physical and Human Geography',
            'KISWAHILI': 'Swahili Language and Culture',
            'RUNYNKORE – RUKIGA': 'Local Ugandan Languages',
            'MATHEMATICS': 'Mathematical Concepts and Applications',
            'AGRICULTURE': 'Agricultural Practices and Theory',
            'PHYSICS': 'Physics Principles and Experiments',
            'CHEMISTRY': 'Chemistry and Chemical Reactions',
            'BIOLOGY': 'Biology and Life Sciences',
            'ART': 'Visual Arts and Design',
            'COMMERCE': 'Business and Trade Principles',
            'COMPUTER': 'Information Technology and Computing',
            'ENTREPRENEURSHIP EDUCATION': 'Business and Entrepreneurial Skills',
            'PHYSICAL EDUCATION': 'Physical Fitness and Sports',
            'GENERAL PAPER': 'Focuses on general knowledge, current affairs, and critical thinking skills',
            'SUBSIDIARY MATHEMATICS': 'Introduction to basic mathematical concepts such as algebra, geometry, and calculus',
            'SUBSIDIARY COMPUTER': 'Covers fundamentals of computer science: hardware, software, programming',
            'FOOD AND NUTRITION': 'Science of food, nutrition, and its relation to health and well-being',
            'ECONOMICS': 'Economic principles, market structures, and role in society',
            'ENTREPRENEURSHIP': 'Basics of starting and managing businesses: marketing, finance, innovation',
            'LITERATURE': 'Analyzes English literary works, from classical to modern, with a focus on themes and writing styles',
            'FINE ART': 'Visual arts: painting, sculpture, digital media, creation and analysis'
        };

        // Try to find exact match first
        if (descriptions[subjectName.toUpperCase()]) {
            return descriptions[subjectName.toUpperCase()];
        }

        // Try partial matches
        for (const [key, description] of Object.entries(descriptions)) {
            if (subjectName.toUpperCase().includes(key) || key.includes(subjectName.toUpperCase())) {
                return description;
            }
        }

        // Default description
        const levelText = this.level === 'olevel' ? 'Comprehensive study of' : 'Advanced study of';
        return `${levelText} ${subjectName.toLowerCase()} principles and applications.`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Auto-initialize if container exists
document.addEventListener('DOMContentLoaded', function() {
    // For O-Level page
    const olevelContainer = document.getElementById('olevel-subjects-container');
    if (olevelContainer) {
        const olevelLoader = new SubjectsLoader('olevel-subjects-container', 'olevel');
        olevelLoader.loadSubjects();
    }

    // For A-Level page
    const alevelContainer = document.getElementById('alevel-subjects-container');
    if (alevelContainer) {
        const alevelLoader = new SubjectsLoader('alevel-subjects-container', 'alevel');
        alevelLoader.loadSubjects();
    }
});

// Export for manual use
window.SubjectsLoader = SubjectsLoader; 
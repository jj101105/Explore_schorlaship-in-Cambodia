# Sample scholarship data (same as your JavaScript)
scholarship_data = [
    {
        "skill": "programming",
        "level": "bachelor",
        "location": "phnom penh",
        "scholarship": "Tech Talent Scholarship",
        "university": "Royal University of Phnom Penh",
        "page": "rupp.html"
    },
    {
        "skill": "math",
        "level": "bachelor",
        "location": "kampong cham",
        "scholarship": "STEM Provincial Scholarship",
        "university": "Kampong Cham Institute of Technology",
        "page": "kcit.html"
    },
    {
        "skill": "english",
        "level": "associate",
        "location": "siem reap",
        "scholarship": "Language Excellence Grant",
        "university": "Siem Reap College",
        "page": "src.html"
    },
    {
        "skill": "leadership",
        "level": "master",
        "location": "phnom penh",
        "scholarship": "Future Leaders Fellowship",
        "university": "Paññāsāstra University",
        "page": "puc.html"
    },
    {
        "skill": "art",
        "level": "bachelor",
        "location": "battambang",
        "scholarship": "Creative Arts Award",
        "university": "Battambang Arts University",
        "page": "bau.html"
    },
    {
        "skill": "math",
        "level": "bachelor",
        "location": "phnom penh",
        "scholarship": "Mathematics Excellence Scholarship",
        "university": "Institute of Technology of Cambodia",
        "page": "itc.html"
    },
    {
        "skill": "english",
        "level": "bachelor",
        "location": "phnom penh",
        "scholarship": "English Communication Grant",
        "university": "Royal University of Phnom Penh",
        "page": "rupp.html"
    },
    {
        "skill": "art",
        "level": "bachelor",
        "location": "phnom penh",
        "scholarship": "Phnom Penh Art Talent Award",
        "university": "Royal University of Fine Arts",
        "page": "rupa.html"
    },
    {
        "skill": "leadership",
        "level": "bachelor",
        "location": "phnom penh",
        "scholarship": "Emerging Leaders Scholarship",
        "university": "Cambodia University of Management and Economics",
        "page": "cume.html"
    }
]

# Get user input
print("Welcome to the Cambodia Scholarship Recommender")
level = input("Enter your study level (associate/bachelor/master): ").lower()
skill = input("Enter your strongest skill (programming/math/english/art/leadership): ").lower()
location = input("Enter your preferred province (phnom penh/siem reap/kampong cham/battambang/takeo): ").lower()

# Find matching scholarships
matches = [
    s for s in scholarship_data
    if s["level"] == level and s["skill"] == skill and s["location"] == location
]

# Display results
if not matches:
    print("\n❌ No matching recommendations found.")
else:
    print("\n🎓 Recommended Scholarships for You:")
    for s in matches:
        print(f"""
📌 Scholarship: {s['scholarship']}
🏫 University: {s['university']}
📍 Location: {s['location'].title()}
🔗 Visit: {s['page']} (for details)
""")

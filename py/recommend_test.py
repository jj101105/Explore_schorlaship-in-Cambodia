import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# === Sample data ===
scholarships = pd.DataFrame({
    'id': [1, 2, 3],
    'name': [
        'AUPP Full Scholarship',
        'Engineering Government Grant',
        'IT Master Scholarship'
    ],
    'tags': [
        'Bachelor Phnom Penh Full IT',
        'Bachelor Siem Reap Engineering Government',
        'Master Phnom Penh IT Full'
    ]
})

user_profile = "Bachelor Phnom Penh IT Full"

corpus = scholarships['tags'].tolist() + [user_profile]

vectorizer = TfidfVectorizer()
tfidf_matrix = vectorizer.fit_transform(corpus)

similarity = cosine_similarity(tfidf_matrix[-1], tfidf_matrix[:-1])

scholarships['score'] = similarity[0]

recommended = scholarships.sort_values(by='score', ascending=False)
print("🎓 Top Recommended Scholarships:")
print(recommended[['name', 'score']])

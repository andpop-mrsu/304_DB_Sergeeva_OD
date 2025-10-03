import pandas as pd

ratings = pd.read_csv('dataset/ratings.csv')

min_user = ratings['userId'].min()
max_user = ratings['userId'].max()

min_count = len(ratings[ratings['userId'] == min_user])
max_count = len(ratings[ratings['userId'] == max_user])

with open('ratings_count.txt', 'w') as f:
    f.write(f"{min_user} {min_count}\n")
    f.write(f"{max_user} {max_count}\n")

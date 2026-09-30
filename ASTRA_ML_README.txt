ASTRA AI - MACHINE LEARNING MODULE
==================================

WHAT WAS ADDED
---------------
ASTRA now includes a real supervised Machine Learning component:

Algorithm: Multinomial Naive Bayes text classification
Task: Student-question intent classification
Use: ML-assisted knowledge matching and related-question recommendations
Training data: database/ml_dataset.csv
Trained model: database/ml/ml_model.json

INTENT CLASSES
--------------
admission
tuition
courses
enrollment
registrar
guidance
library
student_activities
events
map
personnel
emergency
food
computer_lab
scholarship

HOW IT WORKS
------------
1. The student enters a question.
2. ASTRA tokenizes and normalizes the text.
3. The trained Multinomial Naive Bayes model predicts an intent.
4. The predicted intent is used to boost matching knowledge-base answers.
5. The same ML prediction is used by suggestions.php to rank related questions.
6. Predictions are stored in ml_interactions for evaluation/future retraining.
7. OpenRouter remains the fallback for questions that the local knowledge system
   cannot confidently answer.

XAMPP SETUP
-----------
1. Extract the project into C:\\xampp\\htdocs\\ (for example C:\\xampp\\htdocs\\111).
2. Start Apache and MySQL in XAMPP.
3. Import database/database.sql into phpMyAdmin.
4. Open the project normally.
5. The included model file is already trained and ready to use.

RETRAINING
----------
The admin dashboard now contains a "Machine Learning" card.
Log in as administrator and open that page to retrain the model after editing
database/ml_dataset.csv.

You can add more training examples using this CSV format:
intent,text
library,Where can I borrow a book?

After editing the dataset, use the Machine Learning page again. It rebuilds
 database/ml/ml_model.json.

IMPORTANT THESIS NOTE
---------------------
This is not just keyword matching. The ML module uses a supervised learning
algorithm (Multinomial Naive Bayes), estimates class probabilities, and applies
the learned model to new student questions. The model is separate from the
OpenRouter generative AI API.

For thesis evaluation, keep the training dataset and report the number of
training examples, intent classes, and model version. A larger labeled dataset
will improve reliability.


LEARNED QUESTION/ANSWER MEMORY
-------------------------------
ASTRA also includes retrieval-based learning for questions that are not in
the official knowledge table.

When OpenRouter successfully answers an unknown question:
1. ASTRA saves the original question and answer in learned_qa.
2. ASTRA also saves the Multinomial Naive Bayes predicted intent.
3. When another student asks a similar question, ASTRA compares the new
   question with previous learned questions.
4. If the similarity is high enough, ASTRA retrieves the previous answer.
5. The answer is returned without requiring another OpenRouter generation.

IMPORTANT:
Multinomial Naive Bayes does NOT generate the answer. It classifies the
question's intent. The learned Q&A component performs retrieval of previous
answers. OpenRouter generates the initial answer when the official knowledge
base and learned Q&A memory cannot answer the question.

To enable this feature, import the updated database/database.sql so the
learned_qa table is created.

UPDATED ML TRAINING & EVALUATION FEATURES (2026-09-26)
------------------------------------------------------
The admin Machine Learning page now provides a complete training workflow:

1. Click "Train + Evaluate Model".
2. ASTRA automatically regenerates a larger labeled dataset with natural-language,
   paraphrase, and selected Taglish variations across all 15 intents.
3. The Multinomial Naive Bayes model is rebuilt and saved to database/ml/ml_model.json.
4. ASTRA evaluates the newly trained model using a separate built-in evaluation set
   containing 75 questions (5 per intent).
5. The page displays accuracy, macro precision, macro recall, macro F1, per-intent
   recall, and misclassification details.
6. The latest evaluation is saved to database/ml/ml_evaluation.json.

The generated dataset currently contains more than 1,500 labeled examples across
15 intent classes. The evaluation set is separate from the generated training CSV.
For a thesis, replace or supplement the built-in evaluation questions with a
carefully labeled real-world test set collected from students. Do not report the
built-in 75-question score as the final field accuracy without explaining the test
set and its limitations.

CGCI PERSONNEL ORGANIZATIONAL CHART
------------------------------------
The public CGCI Personnel page now displays each department as an organizational chart.
The hierarchy is controlled from Admin > CGCI Personnel using the "Reports To" field.
Use "Top level / Department Head" for the head or a top-level employee. Select another
employee as the supervisor to place a staff member below that person in the public chart.

The project automatically adds the personnel parent_id and sort_order fields when needed.
A manual migration is also provided at database/personnel_org_migration.sql.

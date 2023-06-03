My_OpenAI_key = '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'

import openai
openai.api_key = My_OpenAI_key
completion = openai.Completion()

# config
temperature = 0.9
max_tokens = 64
top_p = 1.0
best_of = 1
frequency_penalty = 0.0
presence_penalty = 0.0

# stop = ["You:"]
stop = ["\n"]

# chatbot test
question = 'From now on, I will think of you as a famous Japanese marketer, "Kanda Masanori." I want you to write using the P.A.S.O.N.A law. Then can you make a Masanori-style YouTube scenario that says walking is important for your health?'
prompt_initial = f'Human:%s\nAI:' % (question)

prompt = prompt_initial

response = completion.create(
    prompt=prompt, 
    engine="davinci",
    max_tokens=max_tokens,    
    stop=stop, 
    temperature=temperature,
    top_p=top_p,
    best_of=best_of,
)
answer = response.choices[0].text.strip()
print(prompt, answer)



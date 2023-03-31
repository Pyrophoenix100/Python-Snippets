def flushBuffer(lines=32):
    for i in range(lines):
        print("\n")

def conditionalInput(prompt, condition):
    while (True):
        inp = input(prompt)
        if condition(inp) == True:
            return inp
        else:
            print("Please enter a valid option")
            continue

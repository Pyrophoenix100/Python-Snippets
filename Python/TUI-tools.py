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


# CTRL+Z to stop input.
def multilineInput(prompt):
    print(prompt)
    contents = []
    while True:
        try:
            line = input()
        except EOFError:
            break
        contents.append(line)
    return "\n".join(contents)

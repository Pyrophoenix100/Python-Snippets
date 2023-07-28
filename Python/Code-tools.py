def flushBuffer(lines=32):
    for i in range(lines):
        print("\n")

def conditionalInput(prompt, condition, condString = ""):
    while (True):
        inp = input(prompt)
        try:
            if condition(inp) == True:
                return inp
            else:
                print("Please enter a valid option " + str(condString))
                continue
        except:
            pass

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

# Constant Definition
options = 2
while True:
    print("Code Tools v0.01 ================")
    print("0: Exit                          ")
    print("1: Switch Quotes                 ")
    inp = int(conditionalInput("Select an option > ", lambda a : -1 < int(a) < options))
    if (inp == 0): 
        exit()
    if (inp == 1):
        print("Switch quotes which way?")
        print("1: \' -> \" ")
        print("2: \" -> \' ")
        inp1 = int(conditionalInput("Select an option > ", lambda a : 0 < int(a) <= 2))
        if (inp1 == 1):
            flushBuffer()
            output = multilineInput("Switching Quotes (\' -> \"), paste text (Ctrl-Z to stop)...").replace("\'", "\"")
            print("OUTPUT ==================")
            print(output)
            input("Enter to continue...")
        elif (inp1 == 2):
            flushBuffer()
            output = multilineInput("Switching Quotes (\" -> \'), paste text (Ctrl-Z to stop)...").replace("\"", "\'")
            print("OUTPUT ==================")
            print(output)
            input("Enter to continue...")
    flushBuffer()